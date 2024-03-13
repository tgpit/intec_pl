<?php
namespace Avito\Export\Trading\Action\OrderAccept;

use Avito\Export\Assert;
use Avito\Export\Concerns;
use Avito\Export\Config;
use Avito\Export\Glossary;
use Avito\Export\Utils\ArrayHelper;
use Avito\Export\Api;
use Avito\Export\Trading;
use Avito\Export\Trading\Action\Reference as TradingReference;
use Bitrix\Main\Event;

class Action extends TradingReference\Action
{
	use Concerns\HasLocale;

	/** @var Command */
	protected $command;
	/** @var Trading\Entity\Sale\Order $order */
	protected $order;
	/** @var Data\ItemsMap $itemsMap */
	protected $itemsMap;
	protected $siteId;
	protected $state;

	public function __construct(Trading\Setup\Model $trading, Command $command)
	{
		parent::__construct($trading, $command);

		$this->state = Trading\State\Repository::forOrder($command->order()->id());
	}

	public function process() : void
	{
		if ($this->find()) { return; }

		$this->mapItems();
		$this->resolveSite();

		$this->create();
		$this->initialize();
		$this->fillDates();
		$this->fillTradingPlatform();
		$this->fillProperties();
		$this->fillBasket();
		$this->fillShipment();
		$this->fillPayment();
		$this->fillTrackingNumber();
		$this->fillContact();
		$this->fillRelatedProperties();
		$this->fillStatus();
		$this->finalize();
		$this->linkChat();
		$this->fireBeforeSave();
		$this->save();
		$this->fireAfterSave();
		$this->commit();
	}

	protected function find() : bool
	{
		return $this->environment->orderRegistry()->search($this->command->order()->id()) !== null;
	}

	protected function resolveSite() : void
	{
		$personTypeSites = $this->environment->personType()->sites($this->settings->personType());
		$feedSites = $this->trading->getExchange()->fillFeed()->allSites();
		$commonSites = array_intersect($personTypeSites, $feedSites);
		$commonCount = count($commonSites);

		if ($commonCount === 0)
		{
			$siteId = reset($personTypeSites);
		}
		else if ($commonCount === 1)
		{
			$siteId = reset($commonSites);
		}
		else
		{
			$productSites = $this->environment->product()->sites($feedSites, $this->itemsMap->values());
			$matchedSites = array_intersect($personTypeSites, $productSites);
			$matchedCount = count($matchedSites);

			if ($matchedCount === 0)
			{
				$siteId = reset($personTypeSites);
			}
			else
			{
				$siteId = reset($matchedSites);
			}
		}

		$this->siteId = $siteId;
	}

	protected function create() : void
	{
		$personTypeId = $this->settings->personType();

		$this->order = $this->environment->orderRegistry()->create($this->siteId, $personTypeId);
	}

	protected function initialize() : void
	{
		$this->order->initialize();
	}

	protected function finalize() : void
	{
		$this->order->finalize();
	}

	protected function fillDates() : void
	{
		if ($this->command->order()->status() === Trading\Service\Status::STATUS_ON_CONFIRMATION) { return; }

		$this->order->fillDateInsert($this->command->order()->createdAt());
	}

	protected function fillTradingPlatform() : void
	{
		$this->order->fillTradingPlatform(
			$this->command->order()->id(),
			$this->trading->getId(),
			[
				'DELIVERY_TYPE' => $this->command->order()->delivery()->serviceType(),
				'NUMBER' => $this->command->order()->number(),
			]
		);
	}

	protected function fillBasket() : void
	{
		$this->order->createEmptyBasket();

		$basketData = $this->basketData();

		/** @var Api\OrderManagement\Model\Order\Item $item */
		foreach ($this->command->order()->items() as $item)
		{
			$productId = $this->itemsMap->get($item);
			$productData = [];

			if ($productId !== null)
			{
				$productData = $basketData[$productId] ?? [];
			}

			$this->order->addProduct($productId, $item->count(), array_merge([
				'NAME' => $item->title(),
				'PRICE' => $item->prices()->price(),
				'EXTERNAL_ORDER_ID' => $this->command->order()->id(),
				'EXTERNAL_ID' => $item->avitoId(),
			], $productData));
		}
	}

	protected function mapItems() : void
	{
		$command = new Stage\MapItems($this->environment, $this->trading);

		$this->itemsMap = $command->execute($this->command->order()->items());
	}

	protected function basketData() : array
	{
		return $this->environment->product()->basketData($this->itemsMap->values(), $this->siteId);
	}

	protected function fillShipment() : void
	{
		$deliveryId = $this->settings->delivery();

		if ($deliveryId === null) { return; }

		$this->order->fillShipment($deliveryId, [
			'PRICE' => 0,
			'DELIVERY_NAME' => $this->command->order()->delivery()->serviceName(),
		]);
	}

	protected function fillTrackingNumber() : void
	{
		$delivery = $this->command->order()->delivery();
		$trackingNumber = $delivery->trackingNumber();

		if ($delivery->serviceType() !== Trading\Service\Delivery::TYPE_PVZ) { return; }

		if ($trackingNumber === null) { return; }

		$setResult = $this->order->fillTrackingNumber($trackingNumber);

		if ($setResult->isSuccess())
		{
			$this->state->set('TRACKING_NUMBER', $trackingNumber);
		}
	}

	protected function fillPayment() : void
	{
		$paySystemId = $this->settings->paySystem();

		if ($paySystemId === null) { return; }

		$this->order->fillPayment($paySystemId);
	}

	protected function fillProperties() : void
	{
		$this->fillProfileProperties();
		$this->fillDeliveryProperties();
		$this->fillScheduleProperties();
		$this->fillOrderNumberAvito();
	}

	protected function fillProfileProperties() : void
	{
		$profileId = $this->settings->buyerProfile();

		if ($profileId === null) { return; }

		$properties = $this->environment->buyerProfile()->properties($profileId);

		$this->order->fillProperties($properties);
	}

	protected function fillDeliveryProperties() : void
	{
		$this->fillCourierProperties();
		$this->fillTerminalProperties();
	}

	protected function fillCourierProperties() : void
	{
		$courierInfo = $this->command->order()->delivery()->courierInfo();

		if ($courierInfo === null) { return; }

		$this->order->fillProperties([
			'DELIVERY_ADDRESS' => $courierInfo->address(),
		], $this->settings);

		$this->order->fillUserDescription($courierInfo->comment());
	}

	protected function fillTerminalProperties() : void
	{
		$terminalInfo = $this->command->order()->delivery()->terminalInfo();

		if ($terminalInfo === null) { return; }

		$this->order->fillProperties([
			'DELIVERY_ADDRESS' => sprintf('%s #%s', $terminalInfo->address(), $terminalInfo->code()),
		], $this->settings);
	}

	protected function fillScheduleProperties() : void
	{
		$values = $this->command->order()->schedules()->meaningfulValues();
		$values = ArrayHelper::prefixKeys($values, 'SCHEDULE_');

		$this->order->fillProperties($values, $this->settings);
		$this->state->setHash('SCHEDULE', $values);
	}

	protected function fillOrderNumberAvito() : void
	{
		$this->order->fillProperties([
			'ORDER_NUMBER_AVITO' => $this->command->order()->number()
		], $this->settings);
	}

	protected function fillRelatedProperties() : void
	{
		$this->order->fillRelatedProperties();
	}

	protected function fillStatus() : void
	{
		$status = $this->command->order()->status();
		$queue = $this->service->status()->statusQueue($status);
		$failed = false;

		$this->state->set('STATUS', $status); // commit status for send/status ignore

		foreach ($queue as $queueStatus)
		{
			if (!$this->applyStatus($queueStatus))
			{
				$failed = true;
				break;
			}
		}

		if ($failed)
		{
			$this->state->unset('STATUS');
		}
	}

	protected function fillContact() : void
	{
		if (!($this->order instanceof Trading\Entity\SaleCrm\Order)) { return; }

		$contactIds = $this->anonymousContactIds();

		$this->order->fillContacts($contactIds);
	}

	protected function anonymousContactIds() : array
	{
		if (!($this->environment instanceof Trading\Entity\SaleCrm\Container)) { return []; }

		$anonymousContact = $this->environment->contactRegistry()->anonymous($this->settings->personType());

		if ($anonymousContact->installed())
		{
			$contactIds = $anonymousContact->id();
		}
		else
		{
			$contactIds = $anonymousContact->install();
		}

		return $contactIds;
	}

	protected function applyStatus(string $externalStatus) : bool
	{
		$status = $this->settings->statusIn($externalStatus);

		if ($status === null) { return true; }

		$setResult = $this->order->fillStatus($status);

		return $setResult->isSuccess();
	}

	protected function linkChat() : void
	{
		if (!($this->environment instanceof Trading\Entity\SaleCrm\Container)) { return; }
		if (!($this->order instanceof Trading\Entity\SaleCrm\Order)) { return; }

		$tokenServiceId = (int)$this->settings->commonSettings()->token()->getServiceId();

		/** @var Api\OrderManagement\Model\Order\Item $item */
		foreach ($this->command->order()->items() as $item)
		{
			if ($item->chatId() === null) { continue; }

			$chat = $this->environment->chatRegistry()->search(
				$tokenServiceId,
				$item->chatId()
			);

			if ($chat === null)
			{
				if ($this->environment->chatRegistry()->configured($tokenServiceId))
				{
					$this->order->waitChat($item->chatId());
				}

				continue;
			}

			$userId = $chat->user();

			if ($userId !== null)
			{
				$this->order->fillUser($userId);
			}

			$this->order->fillContacts($chat->contacts());
			$this->order->linkActivity($chat->activity());
			$this->order->linkChat($chat);
		}
	}

	protected function fireBeforeSave() : void
	{
		$this->fireEvent(Trading\EventActions::ORDER_BEFORE_SAVE, [
			'AVITO_ORDER' => $this->command->order(),
			'ORDER' => $this->order->saleOrder(),
		]);
	}

	protected function save() : void
	{
		$saveResult = $this->order->save();

		Assert::result($saveResult);

		$this->logger->info(self::getLocale('LOG_SUCCESS', [
			'#ACCOUNT_NUMBER#' => $this->order->accountNumber(),
		]), [
			'ENTITY_TYPE' => Glossary::ENTITY_ORDER,
			'ENTITY_ID' => $this->command->order()->number(),
		]);
	}

	protected function fireAfterSave() : void
	{
		$this->fireEvent(Trading\EventActions::ORDER_AFTER_SAVE, [
			'AVITO_ORDER' => $this->command->order(),
			'ORDER' => $this->order->saleOrder(),
		]);
	}

	protected function fireEvent(string $type, array $parameters) : Event
	{
		$event = new Event(Config::getModuleName(), $type, $parameters + [
			'ENVIRONMENT' => $this->environment,
			'TRADING' => $this->trading,
			'SERVICE' => $this->service,
			'STATE' => $this->state,
		]);

		$event->send();

		return $event;
	}

	protected function commit() : void
	{
		$this->state->save();
	}
}