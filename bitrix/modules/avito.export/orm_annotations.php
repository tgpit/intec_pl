<?php

/* ORMENTITYANNOTATION:Avito\Export\Push\Engine\Steps\Stamp\RepositoryTable */
namespace Avito\Export\Push\Engine\Steps\Stamp {
	/**
	 * Model
	 * @see \Avito\Export\Push\Engine\Steps\Stamp\RepositoryTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getPushId()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setPushId(\int|\Bitrix\Main\DB\SqlExpression $pushId)
	 * @method bool hasPushId()
	 * @method bool isPushIdFilled()
	 * @method bool isPushIdChanged()
	 * @method \int getElementId()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setElementId(\int|\Bitrix\Main\DB\SqlExpression $elementId)
	 * @method bool hasElementId()
	 * @method bool isElementIdFilled()
	 * @method bool isElementIdChanged()
	 * @method \int getRegionId()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setRegionId(\int|\Bitrix\Main\DB\SqlExpression $regionId)
	 * @method bool hasRegionId()
	 * @method bool isRegionIdFilled()
	 * @method bool isRegionIdChanged()
	 * @method \string getType()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setType(\string|\Bitrix\Main\DB\SqlExpression $type)
	 * @method bool hasType()
	 * @method bool isTypeFilled()
	 * @method bool isTypeChanged()
	 * @method \string getPrimary()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setPrimary(\string|\Bitrix\Main\DB\SqlExpression $primary)
	 * @method bool hasPrimary()
	 * @method bool isPrimaryFilled()
	 * @method bool isPrimaryChanged()
	 * @method \string remindActualPrimary()
	 * @method \string requirePrimary()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model resetPrimary()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model unsetPrimary()
	 * @method \string fillPrimary()
	 * @method \string getValue()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setValue(\string|\Bitrix\Main\DB\SqlExpression $value)
	 * @method bool hasValue()
	 * @method bool isValueFilled()
	 * @method bool isValueChanged()
	 * @method \string remindActualValue()
	 * @method \string requireValue()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model resetValue()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model unsetValue()
	 * @method \string fillValue()
	 * @method \string getStatus()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setStatus(\string|\Bitrix\Main\DB\SqlExpression $status)
	 * @method bool hasStatus()
	 * @method bool isStatusFilled()
	 * @method bool isStatusChanged()
	 * @method \string remindActualStatus()
	 * @method \string requireStatus()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model resetStatus()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model unsetStatus()
	 * @method \string fillStatus()
	 * @method \int getRepeat()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setRepeat(\int|\Bitrix\Main\DB\SqlExpression $repeat)
	 * @method bool hasRepeat()
	 * @method bool isRepeatFilled()
	 * @method bool isRepeatChanged()
	 * @method \int remindActualRepeat()
	 * @method \int requireRepeat()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model resetRepeat()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model unsetRepeat()
	 * @method \int fillRepeat()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model resetTimestampX()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository getServicePrimary()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository remindActualServicePrimary()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository requireServicePrimary()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model setServicePrimary(\Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository $object)
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model resetServicePrimary()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model unsetServicePrimary()
	 * @method bool hasServicePrimary()
	 * @method bool isServicePrimaryFilled()
	 * @method bool isServicePrimaryChanged()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository fillServicePrimary()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model set($fieldName, $value)
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model reset($fieldName)
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Push\Engine\Steps\Stamp\Model wakeUp($data)
	 */
	class EO_Repository {
		/* @var \Avito\Export\Push\Engine\Steps\Stamp\RepositoryTable */
		static public $dataClass = '\Avito\Export\Push\Engine\Steps\Stamp\RepositoryTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Push\Engine\Steps\Stamp {
	/**
	 * Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getPushIdList()
	 * @method \int[] getElementIdList()
	 * @method \int[] getRegionIdList()
	 * @method \string[] getTypeList()
	 * @method \string[] getPrimaryList()
	 * @method \string[] fillPrimary()
	 * @method \string[] getValueList()
	 * @method \string[] fillValue()
	 * @method \string[] getStatusList()
	 * @method \string[] fillStatus()
	 * @method \int[] getRepeatList()
	 * @method \int[] fillRepeat()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository[] getServicePrimaryList()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Collection getServicePrimaryCollection()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository_Collection fillServicePrimary()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Push\Engine\Steps\Stamp\Model $object)
	 * @method bool has(\Avito\Export\Push\Engine\Steps\Stamp\Model $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model getByPrimary($primary)
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model[] getAll()
	 * @method bool remove(\Avito\Export\Push\Engine\Steps\Stamp\Model $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Push\Engine\Steps\Stamp\Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method Collection merge(?Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Repository_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Push\Engine\Steps\Stamp\RepositoryTable */
		static public $dataClass = '\Avito\Export\Push\Engine\Steps\Stamp\RepositoryTable';
	}
}
namespace Avito\Export\Push\Engine\Steps\Stamp {
	/**
	 * @method static EO_Repository_Query query()
	 * @method static EO_Repository_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Repository_Result getById($id)
	 * @method static EO_Repository_Result getList(array $parameters = [])
	 * @method static EO_Repository_Entity getEntity()
	 * @method static \Avito\Export\Push\Engine\Steps\Stamp\Model createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Push\Engine\Steps\Stamp\Collection createCollection()
	 * @method static \Avito\Export\Push\Engine\Steps\Stamp\Model wakeUpObject($row)
	 * @method static \Avito\Export\Push\Engine\Steps\Stamp\Collection wakeUpCollection($rows)
	 */
	class RepositoryTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Repository_Result exec()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model fetchObject()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Repository_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model fetchObject()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Collection fetchCollection()
	 */
	class EO_Repository_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model createObject($setDefaultValues = true)
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Collection createCollection()
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Model wakeUpObject($row)
	 * @method \Avito\Export\Push\Engine\Steps\Stamp\Collection wakeUpCollection($rows)
	 */
	class EO_Repository_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Push\Engine\Steps\PrimaryMap\RepositoryTable */
namespace Avito\Export\Push\Engine\Steps\PrimaryMap {
	/**
	 * EO_Repository
	 * @see \Avito\Export\Push\Engine\Steps\PrimaryMap\RepositoryTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getPushId()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository setPushId(\int|\Bitrix\Main\DB\SqlExpression $pushId)
	 * @method bool hasPushId()
	 * @method bool isPushIdFilled()
	 * @method bool isPushIdChanged()
	 * @method \string getPrimary()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository setPrimary(\string|\Bitrix\Main\DB\SqlExpression $primary)
	 * @method bool hasPrimary()
	 * @method bool isPrimaryFilled()
	 * @method bool isPrimaryChanged()
	 * @method \string getServiceId()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository setServiceId(\string|\Bitrix\Main\DB\SqlExpression $serviceId)
	 * @method bool hasServiceId()
	 * @method bool isServiceIdFilled()
	 * @method bool isServiceIdChanged()
	 * @method \string remindActualServiceId()
	 * @method \string requireServiceId()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository resetServiceId()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository unsetServiceId()
	 * @method \string fillServiceId()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository resetTimestampX()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository set($fieldName, $value)
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository reset($fieldName)
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository wakeUp($data)
	 */
	class EO_Repository {
		/* @var \Avito\Export\Push\Engine\Steps\PrimaryMap\RepositoryTable */
		static public $dataClass = '\Avito\Export\Push\Engine\Steps\PrimaryMap\RepositoryTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Push\Engine\Steps\PrimaryMap {
	/**
	 * EO_Repository_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getPushIdList()
	 * @method \string[] getPrimaryList()
	 * @method \string[] getServiceIdList()
	 * @method \string[] fillServiceId()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository $object)
	 * @method bool has(\Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository getByPrimary($primary)
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository[] getAll()
	 * @method bool remove(\Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Repository_Collection merge(?EO_Repository_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Repository_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Push\Engine\Steps\PrimaryMap\RepositoryTable */
		static public $dataClass = '\Avito\Export\Push\Engine\Steps\PrimaryMap\RepositoryTable';
	}
}
namespace Avito\Export\Push\Engine\Steps\PrimaryMap {
	/**
	 * @method static EO_Repository_Query query()
	 * @method static EO_Repository_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Repository_Result getById($id)
	 * @method static EO_Repository_Result getList(array $parameters = [])
	 * @method static EO_Repository_Entity getEntity()
	 * @method static \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository_Collection createCollection()
	 * @method static \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository wakeUpObject($row)
	 * @method static \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository_Collection wakeUpCollection($rows)
	 */
	class RepositoryTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Repository_Result exec()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository fetchObject()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Repository_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository fetchObject()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository_Collection fetchCollection()
	 */
	class EO_Repository_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository createObject($setDefaultValues = true)
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository_Collection createCollection()
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository wakeUpObject($row)
	 * @method \Avito\Export\Push\Engine\Steps\PrimaryMap\EO_Repository_Collection wakeUpCollection($rows)
	 */
	class EO_Repository_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Push\Setup\RepositoryTable */
namespace Avito\Export\Push\Setup {
	/**
	 * EO_Repository
	 * @see \Avito\Export\Push\Setup\RepositoryTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Avito\Export\Push\Setup\EO_Repository setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \string getName()
	 * @method \Avito\Export\Push\Setup\EO_Repository setName(\string|\Bitrix\Main\DB\SqlExpression $name)
	 * @method bool hasName()
	 * @method bool isNameFilled()
	 * @method bool isNameChanged()
	 * @method \string remindActualName()
	 * @method \string requireName()
	 * @method \Avito\Export\Push\Setup\EO_Repository resetName()
	 * @method \Avito\Export\Push\Setup\EO_Repository unsetName()
	 * @method \string fillName()
	 * @method \int getFeedId()
	 * @method \Avito\Export\Push\Setup\EO_Repository setFeedId(\int|\Bitrix\Main\DB\SqlExpression $feedId)
	 * @method bool hasFeedId()
	 * @method bool isFeedIdFilled()
	 * @method bool isFeedIdChanged()
	 * @method \int remindActualFeedId()
	 * @method \int requireFeedId()
	 * @method \Avito\Export\Push\Setup\EO_Repository resetFeedId()
	 * @method \Avito\Export\Push\Setup\EO_Repository unsetFeedId()
	 * @method \int fillFeedId()
	 * @method \Avito\Export\Feed\Setup\Model getFeed()
	 * @method \Avito\Export\Feed\Setup\Model remindActualFeed()
	 * @method \Avito\Export\Feed\Setup\Model requireFeed()
	 * @method \Avito\Export\Push\Setup\EO_Repository setFeed(\Avito\Export\Feed\Setup\Model $object)
	 * @method \Avito\Export\Push\Setup\EO_Repository resetFeed()
	 * @method \Avito\Export\Push\Setup\EO_Repository unsetFeed()
	 * @method bool hasFeed()
	 * @method bool isFeedFilled()
	 * @method bool isFeedChanged()
	 * @method \Avito\Export\Feed\Setup\Model fillFeed()
	 * @method array getSettings()
	 * @method \Avito\Export\Push\Setup\EO_Repository setSettings(array|\Bitrix\Main\DB\SqlExpression $settings)
	 * @method bool hasSettings()
	 * @method bool isSettingsFilled()
	 * @method bool isSettingsChanged()
	 * @method array remindActualSettings()
	 * @method array requireSettings()
	 * @method \Avito\Export\Push\Setup\EO_Repository resetSettings()
	 * @method \Avito\Export\Push\Setup\EO_Repository unsetSettings()
	 * @method array fillSettings()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Push\Setup\EO_Repository setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Push\Setup\EO_Repository resetTimestampX()
	 * @method \Avito\Export\Push\Setup\EO_Repository unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 * @method \boolean getAutoUpdate()
	 * @method \Avito\Export\Push\Setup\EO_Repository setAutoUpdate(\boolean|\Bitrix\Main\DB\SqlExpression $autoUpdate)
	 * @method bool hasAutoUpdate()
	 * @method bool isAutoUpdateFilled()
	 * @method bool isAutoUpdateChanged()
	 * @method \boolean remindActualAutoUpdate()
	 * @method \boolean requireAutoUpdate()
	 * @method \Avito\Export\Push\Setup\EO_Repository resetAutoUpdate()
	 * @method \Avito\Export\Push\Setup\EO_Repository unsetAutoUpdate()
	 * @method \boolean fillAutoUpdate()
	 * @method \int getRefreshPeriod()
	 * @method \Avito\Export\Push\Setup\EO_Repository setRefreshPeriod(\int|\Bitrix\Main\DB\SqlExpression $refreshPeriod)
	 * @method bool hasRefreshPeriod()
	 * @method bool isRefreshPeriodFilled()
	 * @method bool isRefreshPeriodChanged()
	 * @method \int remindActualRefreshPeriod()
	 * @method \int requireRefreshPeriod()
	 * @method \Avito\Export\Push\Setup\EO_Repository resetRefreshPeriod()
	 * @method \Avito\Export\Push\Setup\EO_Repository unsetRefreshPeriod()
	 * @method \int fillRefreshPeriod()
	 * @method \string getRefreshTime()
	 * @method \Avito\Export\Push\Setup\EO_Repository setRefreshTime(\string|\Bitrix\Main\DB\SqlExpression $refreshTime)
	 * @method bool hasRefreshTime()
	 * @method bool isRefreshTimeFilled()
	 * @method bool isRefreshTimeChanged()
	 * @method \string remindActualRefreshTime()
	 * @method \string requireRefreshTime()
	 * @method \Avito\Export\Push\Setup\EO_Repository resetRefreshTime()
	 * @method \Avito\Export\Push\Setup\EO_Repository unsetRefreshTime()
	 * @method \string fillRefreshTime()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Push\Setup\EO_Repository set($fieldName, $value)
	 * @method \Avito\Export\Push\Setup\EO_Repository reset($fieldName)
	 * @method \Avito\Export\Push\Setup\EO_Repository unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Push\Setup\EO_Repository wakeUp($data)
	 */
	class EO_Repository {
		/* @var \Avito\Export\Push\Setup\RepositoryTable */
		static public $dataClass = '\Avito\Export\Push\Setup\RepositoryTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Push\Setup {
	/**
	 * EO_Repository_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \string[] getNameList()
	 * @method \string[] fillName()
	 * @method \int[] getFeedIdList()
	 * @method \int[] fillFeedId()
	 * @method \Avito\Export\Feed\Setup\Model[] getFeedList()
	 * @method \Avito\Export\Push\Setup\EO_Repository_Collection getFeedCollection()
	 * @method \Avito\Export\Feed\Setup\EO_Repository_Collection fillFeed()
	 * @method array[] getSettingsList()
	 * @method array[] fillSettings()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 * @method \boolean[] getAutoUpdateList()
	 * @method \boolean[] fillAutoUpdate()
	 * @method \int[] getRefreshPeriodList()
	 * @method \int[] fillRefreshPeriod()
	 * @method \string[] getRefreshTimeList()
	 * @method \string[] fillRefreshTime()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Push\Setup\EO_Repository $object)
	 * @method bool has(\Avito\Export\Push\Setup\EO_Repository $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Push\Setup\EO_Repository getByPrimary($primary)
	 * @method \Avito\Export\Push\Setup\EO_Repository[] getAll()
	 * @method bool remove(\Avito\Export\Push\Setup\EO_Repository $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Push\Setup\EO_Repository_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Push\Setup\EO_Repository current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Repository_Collection merge(?EO_Repository_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Repository_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Push\Setup\RepositoryTable */
		static public $dataClass = '\Avito\Export\Push\Setup\RepositoryTable';
	}
}
namespace Avito\Export\Push\Setup {
	/**
	 * @method static EO_Repository_Query query()
	 * @method static EO_Repository_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Repository_Result getById($id)
	 * @method static EO_Repository_Result getList(array $parameters = [])
	 * @method static EO_Repository_Entity getEntity()
	 * @method static \Avito\Export\Push\Setup\EO_Repository createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Push\Setup\EO_Repository_Collection createCollection()
	 * @method static \Avito\Export\Push\Setup\EO_Repository wakeUpObject($row)
	 * @method static \Avito\Export\Push\Setup\EO_Repository_Collection wakeUpCollection($rows)
	 */
	class RepositoryTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Repository_Result exec()
	 * @method \Avito\Export\Push\Setup\EO_Repository fetchObject()
	 * @method \Avito\Export\Push\Setup\EO_Repository_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Repository_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Push\Setup\EO_Repository fetchObject()
	 * @method \Avito\Export\Push\Setup\EO_Repository_Collection fetchCollection()
	 */
	class EO_Repository_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Push\Setup\EO_Repository createObject($setDefaultValues = true)
	 * @method \Avito\Export\Push\Setup\EO_Repository_Collection createCollection()
	 * @method \Avito\Export\Push\Setup\EO_Repository wakeUpObject($row)
	 * @method \Avito\Export\Push\Setup\EO_Repository_Collection wakeUpCollection($rows)
	 */
	class EO_Repository_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Logger\Table */
namespace Avito\Export\Logger {
	/**
	 * EO_NNM_Object
	 * @see \Avito\Export\Logger\Table
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getSetupType()
	 * @method \Avito\Export\Logger\EO_NNM_Object setSetupType(\string|\Bitrix\Main\DB\SqlExpression $setupType)
	 * @method bool hasSetupType()
	 * @method bool isSetupTypeFilled()
	 * @method bool isSetupTypeChanged()
	 * @method \int getSetupId()
	 * @method \Avito\Export\Logger\EO_NNM_Object setSetupId(\int|\Bitrix\Main\DB\SqlExpression $setupId)
	 * @method bool hasSetupId()
	 * @method bool isSetupIdFilled()
	 * @method bool isSetupIdChanged()
	 * @method \string getSign()
	 * @method \Avito\Export\Logger\EO_NNM_Object setSign(\string|\Bitrix\Main\DB\SqlExpression $sign)
	 * @method bool hasSign()
	 * @method bool isSignFilled()
	 * @method bool isSignChanged()
	 * @method \string getEntityType()
	 * @method \Avito\Export\Logger\EO_NNM_Object setEntityType(\string|\Bitrix\Main\DB\SqlExpression $entityType)
	 * @method bool hasEntityType()
	 * @method bool isEntityTypeFilled()
	 * @method bool isEntityTypeChanged()
	 * @method \string remindActualEntityType()
	 * @method \string requireEntityType()
	 * @method \Avito\Export\Logger\EO_NNM_Object resetEntityType()
	 * @method \Avito\Export\Logger\EO_NNM_Object unsetEntityType()
	 * @method \string fillEntityType()
	 * @method \string getEntityId()
	 * @method \Avito\Export\Logger\EO_NNM_Object setEntityId(\string|\Bitrix\Main\DB\SqlExpression $entityId)
	 * @method bool hasEntityId()
	 * @method bool isEntityIdFilled()
	 * @method bool isEntityIdChanged()
	 * @method \string remindActualEntityId()
	 * @method \string requireEntityId()
	 * @method \Avito\Export\Logger\EO_NNM_Object resetEntityId()
	 * @method \Avito\Export\Logger\EO_NNM_Object unsetEntityId()
	 * @method \string fillEntityId()
	 * @method \int getRegionId()
	 * @method \Avito\Export\Logger\EO_NNM_Object setRegionId(\int|\Bitrix\Main\DB\SqlExpression $regionId)
	 * @method bool hasRegionId()
	 * @method bool isRegionIdFilled()
	 * @method bool isRegionIdChanged()
	 * @method \int remindActualRegionId()
	 * @method \int requireRegionId()
	 * @method \Avito\Export\Logger\EO_NNM_Object resetRegionId()
	 * @method \Avito\Export\Logger\EO_NNM_Object unsetRegionId()
	 * @method \int fillRegionId()
	 * @method \string getLevel()
	 * @method \Avito\Export\Logger\EO_NNM_Object setLevel(\string|\Bitrix\Main\DB\SqlExpression $level)
	 * @method bool hasLevel()
	 * @method bool isLevelFilled()
	 * @method bool isLevelChanged()
	 * @method \string remindActualLevel()
	 * @method \string requireLevel()
	 * @method \Avito\Export\Logger\EO_NNM_Object resetLevel()
	 * @method \Avito\Export\Logger\EO_NNM_Object unsetLevel()
	 * @method \string fillLevel()
	 * @method \string getMessage()
	 * @method \Avito\Export\Logger\EO_NNM_Object setMessage(\string|\Bitrix\Main\DB\SqlExpression $message)
	 * @method bool hasMessage()
	 * @method bool isMessageFilled()
	 * @method bool isMessageChanged()
	 * @method \string remindActualMessage()
	 * @method \string requireMessage()
	 * @method \Avito\Export\Logger\EO_NNM_Object resetMessage()
	 * @method \Avito\Export\Logger\EO_NNM_Object unsetMessage()
	 * @method \string fillMessage()
	 * @method array getContext()
	 * @method \Avito\Export\Logger\EO_NNM_Object setContext(array|\Bitrix\Main\DB\SqlExpression $context)
	 * @method bool hasContext()
	 * @method bool isContextFilled()
	 * @method bool isContextChanged()
	 * @method array remindActualContext()
	 * @method array requireContext()
	 * @method \Avito\Export\Logger\EO_NNM_Object resetContext()
	 * @method \Avito\Export\Logger\EO_NNM_Object unsetContext()
	 * @method array fillContext()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Logger\EO_NNM_Object setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Logger\EO_NNM_Object resetTimestampX()
	 * @method \Avito\Export\Logger\EO_NNM_Object unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Logger\EO_NNM_Object set($fieldName, $value)
	 * @method \Avito\Export\Logger\EO_NNM_Object reset($fieldName)
	 * @method \Avito\Export\Logger\EO_NNM_Object unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Logger\EO_NNM_Object wakeUp($data)
	 */
	class EO_NNM_Object {
		/* @var \Avito\Export\Logger\Table */
		static public $dataClass = '\Avito\Export\Logger\Table';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Logger {
	/**
	 * EO__Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getSetupTypeList()
	 * @method \int[] getSetupIdList()
	 * @method \string[] getSignList()
	 * @method \string[] getEntityTypeList()
	 * @method \string[] fillEntityType()
	 * @method \string[] getEntityIdList()
	 * @method \string[] fillEntityId()
	 * @method \int[] getRegionIdList()
	 * @method \int[] fillRegionId()
	 * @method \string[] getLevelList()
	 * @method \string[] fillLevel()
	 * @method \string[] getMessageList()
	 * @method \string[] fillMessage()
	 * @method array[] getContextList()
	 * @method array[] fillContext()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Logger\EO_NNM_Object $object)
	 * @method bool has(\Avito\Export\Logger\EO_NNM_Object $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Logger\EO_NNM_Object getByPrimary($primary)
	 * @method \Avito\Export\Logger\EO_NNM_Object[] getAll()
	 * @method bool remove(\Avito\Export\Logger\EO_NNM_Object $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Logger\EO__Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Logger\EO_NNM_Object current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO__Collection merge(?EO__Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO__Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Logger\Table */
		static public $dataClass = '\Avito\Export\Logger\Table';
	}
}
namespace Avito\Export\Logger {
	/**
	 * @method static EO__Query query()
	 * @method static EO__Result getByPrimary($primary, array $parameters = [])
	 * @method static EO__Result getById($id)
	 * @method static EO__Result getList(array $parameters = [])
	 * @method static EO__Entity getEntity()
	 * @method static \Avito\Export\Logger\EO_NNM_Object createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Logger\EO__Collection createCollection()
	 * @method static \Avito\Export\Logger\EO_NNM_Object wakeUpObject($row)
	 * @method static \Avito\Export\Logger\EO__Collection wakeUpCollection($rows)
	 */
	class Table extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO__Result exec()
	 * @method \Avito\Export\Logger\EO_NNM_Object fetchObject()
	 * @method \Avito\Export\Logger\EO__Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO__Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Logger\EO_NNM_Object fetchObject()
	 * @method \Avito\Export\Logger\EO__Collection fetchCollection()
	 */
	class EO__Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Logger\EO_NNM_Object createObject($setDefaultValues = true)
	 * @method \Avito\Export\Logger\EO__Collection createCollection()
	 * @method \Avito\Export\Logger\EO_NNM_Object wakeUpObject($row)
	 * @method \Avito\Export\Logger\EO__Collection wakeUpCollection($rows)
	 */
	class EO__Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Trading\State\RepositoryTable */
namespace Avito\Export\Trading\State {
	/**
	 * Model
	 * @see \Avito\Export\Trading\State\RepositoryTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getOrderId()
	 * @method \Avito\Export\Trading\State\Model setOrderId(\string|\Bitrix\Main\DB\SqlExpression $orderId)
	 * @method bool hasOrderId()
	 * @method bool isOrderIdFilled()
	 * @method bool isOrderIdChanged()
	 * @method \string getName()
	 * @method \Avito\Export\Trading\State\Model setName(\string|\Bitrix\Main\DB\SqlExpression $name)
	 * @method bool hasName()
	 * @method bool isNameFilled()
	 * @method bool isNameChanged()
	 * @method \string getValue()
	 * @method \Avito\Export\Trading\State\Model setValue(\string|\Bitrix\Main\DB\SqlExpression $value)
	 * @method bool hasValue()
	 * @method bool isValueFilled()
	 * @method bool isValueChanged()
	 * @method \string remindActualValue()
	 * @method \string requireValue()
	 * @method \Avito\Export\Trading\State\Model resetValue()
	 * @method \Avito\Export\Trading\State\Model unsetValue()
	 * @method \string fillValue()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Trading\State\Model setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Trading\State\Model resetTimestampX()
	 * @method \Avito\Export\Trading\State\Model unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Trading\State\Model set($fieldName, $value)
	 * @method \Avito\Export\Trading\State\Model reset($fieldName)
	 * @method \Avito\Export\Trading\State\Model unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Trading\State\Model wakeUp($data)
	 */
	class EO_Repository {
		/* @var \Avito\Export\Trading\State\RepositoryTable */
		static public $dataClass = '\Avito\Export\Trading\State\RepositoryTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Trading\State {
	/**
	 * Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getOrderIdList()
	 * @method \string[] getNameList()
	 * @method \string[] getValueList()
	 * @method \string[] fillValue()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Trading\State\Model $object)
	 * @method bool has(\Avito\Export\Trading\State\Model $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Trading\State\Model getByPrimary($primary)
	 * @method \Avito\Export\Trading\State\Model[] getAll()
	 * @method bool remove(\Avito\Export\Trading\State\Model $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Trading\State\Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Trading\State\Model current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method Collection merge(?Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Repository_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Trading\State\RepositoryTable */
		static public $dataClass = '\Avito\Export\Trading\State\RepositoryTable';
	}
}
namespace Avito\Export\Trading\State {
	/**
	 * @method static EO_Repository_Query query()
	 * @method static EO_Repository_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Repository_Result getById($id)
	 * @method static EO_Repository_Result getList(array $parameters = [])
	 * @method static EO_Repository_Entity getEntity()
	 * @method static \Avito\Export\Trading\State\Model createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Trading\State\Collection createCollection()
	 * @method static \Avito\Export\Trading\State\Model wakeUpObject($row)
	 * @method static \Avito\Export\Trading\State\Collection wakeUpCollection($rows)
	 */
	class RepositoryTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Repository_Result exec()
	 * @method \Avito\Export\Trading\State\Model fetchObject()
	 * @method \Avito\Export\Trading\State\Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Repository_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Trading\State\Model fetchObject()
	 * @method \Avito\Export\Trading\State\Collection fetchCollection()
	 */
	class EO_Repository_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Trading\State\Model createObject($setDefaultValues = true)
	 * @method \Avito\Export\Trading\State\Collection createCollection()
	 * @method \Avito\Export\Trading\State\Model wakeUpObject($row)
	 * @method \Avito\Export\Trading\State\Collection wakeUpCollection($rows)
	 */
	class EO_Repository_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Trading\Queue\Table */
namespace Avito\Export\Trading\Queue {
	/**
	 * EO_NNM_Object
	 * @see \Avito\Export\Trading\Queue\Table
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \int getSetupId()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object setSetupId(\int|\Bitrix\Main\DB\SqlExpression $setupId)
	 * @method bool hasSetupId()
	 * @method bool isSetupIdFilled()
	 * @method bool isSetupIdChanged()
	 * @method \int remindActualSetupId()
	 * @method \int requireSetupId()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object resetSetupId()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object unsetSetupId()
	 * @method \int fillSetupId()
	 * @method \string getPath()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object setPath(\string|\Bitrix\Main\DB\SqlExpression $path)
	 * @method bool hasPath()
	 * @method bool isPathFilled()
	 * @method bool isPathChanged()
	 * @method \string remindActualPath()
	 * @method \string requirePath()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object resetPath()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object unsetPath()
	 * @method \string fillPath()
	 * @method array getData()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object setData(array|\Bitrix\Main\DB\SqlExpression $data)
	 * @method bool hasData()
	 * @method bool isDataFilled()
	 * @method bool isDataChanged()
	 * @method array remindActualData()
	 * @method array requireData()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object resetData()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object unsetData()
	 * @method array fillData()
	 * @method \Bitrix\Main\Type\DateTime getExecDate()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object setExecDate(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $execDate)
	 * @method bool hasExecDate()
	 * @method bool isExecDateFilled()
	 * @method bool isExecDateChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualExecDate()
	 * @method \Bitrix\Main\Type\DateTime requireExecDate()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object resetExecDate()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object unsetExecDate()
	 * @method \Bitrix\Main\Type\DateTime fillExecDate()
	 * @method \int getExecCount()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object setExecCount(\int|\Bitrix\Main\DB\SqlExpression $execCount)
	 * @method bool hasExecCount()
	 * @method bool isExecCountFilled()
	 * @method bool isExecCountChanged()
	 * @method \int remindActualExecCount()
	 * @method \int requireExecCount()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object resetExecCount()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object unsetExecCount()
	 * @method \int fillExecCount()
	 * @method \int getInterval()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object setInterval(\int|\Bitrix\Main\DB\SqlExpression $interval)
	 * @method bool hasInterval()
	 * @method bool isIntervalFilled()
	 * @method bool isIntervalChanged()
	 * @method \int remindActualInterval()
	 * @method \int requireInterval()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object resetInterval()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object unsetInterval()
	 * @method \int fillInterval()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object set($fieldName, $value)
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object reset($fieldName)
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Trading\Queue\EO_NNM_Object wakeUp($data)
	 */
	class EO_NNM_Object {
		/* @var \Avito\Export\Trading\Queue\Table */
		static public $dataClass = '\Avito\Export\Trading\Queue\Table';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Trading\Queue {
	/**
	 * EO__Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \int[] getSetupIdList()
	 * @method \int[] fillSetupId()
	 * @method \string[] getPathList()
	 * @method \string[] fillPath()
	 * @method array[] getDataList()
	 * @method array[] fillData()
	 * @method \Bitrix\Main\Type\DateTime[] getExecDateList()
	 * @method \Bitrix\Main\Type\DateTime[] fillExecDate()
	 * @method \int[] getExecCountList()
	 * @method \int[] fillExecCount()
	 * @method \int[] getIntervalList()
	 * @method \int[] fillInterval()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Trading\Queue\EO_NNM_Object $object)
	 * @method bool has(\Avito\Export\Trading\Queue\EO_NNM_Object $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object getByPrimary($primary)
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object[] getAll()
	 * @method bool remove(\Avito\Export\Trading\Queue\EO_NNM_Object $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Trading\Queue\EO__Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO__Collection merge(?EO__Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO__Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Trading\Queue\Table */
		static public $dataClass = '\Avito\Export\Trading\Queue\Table';
	}
}
namespace Avito\Export\Trading\Queue {
	/**
	 * @method static EO__Query query()
	 * @method static EO__Result getByPrimary($primary, array $parameters = [])
	 * @method static EO__Result getById($id)
	 * @method static EO__Result getList(array $parameters = [])
	 * @method static EO__Entity getEntity()
	 * @method static \Avito\Export\Trading\Queue\EO_NNM_Object createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Trading\Queue\EO__Collection createCollection()
	 * @method static \Avito\Export\Trading\Queue\EO_NNM_Object wakeUpObject($row)
	 * @method static \Avito\Export\Trading\Queue\EO__Collection wakeUpCollection($rows)
	 */
	class Table extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO__Result exec()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object fetchObject()
	 * @method \Avito\Export\Trading\Queue\EO__Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO__Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object fetchObject()
	 * @method \Avito\Export\Trading\Queue\EO__Collection fetchCollection()
	 */
	class EO__Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object createObject($setDefaultValues = true)
	 * @method \Avito\Export\Trading\Queue\EO__Collection createCollection()
	 * @method \Avito\Export\Trading\Queue\EO_NNM_Object wakeUpObject($row)
	 * @method \Avito\Export\Trading\Queue\EO__Collection wakeUpCollection($rows)
	 */
	class EO__Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\OrderTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Order
	 * @see \Bitrix\Sale\Internals\OrderTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Bitrix\Sale\Internals\EO_Order setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \string getLid()
	 * @method \Bitrix\Sale\Internals\EO_Order setLid(\string|\Bitrix\Main\DB\SqlExpression $lid)
	 * @method bool hasLid()
	 * @method bool isLidFilled()
	 * @method bool isLidChanged()
	 * @method \string remindActualLid()
	 * @method \string requireLid()
	 * @method \Bitrix\Sale\Internals\EO_Order resetLid()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetLid()
	 * @method \string fillLid()
	 * @method \string getAccountNumber()
	 * @method \Bitrix\Sale\Internals\EO_Order setAccountNumber(\string|\Bitrix\Main\DB\SqlExpression $accountNumber)
	 * @method bool hasAccountNumber()
	 * @method bool isAccountNumberFilled()
	 * @method bool isAccountNumberChanged()
	 * @method \string remindActualAccountNumber()
	 * @method \string requireAccountNumber()
	 * @method \Bitrix\Sale\Internals\EO_Order resetAccountNumber()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetAccountNumber()
	 * @method \string fillAccountNumber()
	 * @method \string getTrackingNumber()
	 * @method \Bitrix\Sale\Internals\EO_Order setTrackingNumber(\string|\Bitrix\Main\DB\SqlExpression $trackingNumber)
	 * @method bool hasTrackingNumber()
	 * @method bool isTrackingNumberFilled()
	 * @method bool isTrackingNumberChanged()
	 * @method \string remindActualTrackingNumber()
	 * @method \string requireTrackingNumber()
	 * @method \Bitrix\Sale\Internals\EO_Order resetTrackingNumber()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetTrackingNumber()
	 * @method \string fillTrackingNumber()
	 * @method \int getPaySystemId()
	 * @method \Bitrix\Sale\Internals\EO_Order setPaySystemId(\int|\Bitrix\Main\DB\SqlExpression $paySystemId)
	 * @method bool hasPaySystemId()
	 * @method bool isPaySystemIdFilled()
	 * @method bool isPaySystemIdChanged()
	 * @method \int remindActualPaySystemId()
	 * @method \int requirePaySystemId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetPaySystemId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetPaySystemId()
	 * @method \int fillPaySystemId()
	 * @method \int getDeliveryId()
	 * @method \Bitrix\Sale\Internals\EO_Order setDeliveryId(\int|\Bitrix\Main\DB\SqlExpression $deliveryId)
	 * @method bool hasDeliveryId()
	 * @method bool isDeliveryIdFilled()
	 * @method bool isDeliveryIdChanged()
	 * @method \int remindActualDeliveryId()
	 * @method \int requireDeliveryId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDeliveryId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDeliveryId()
	 * @method \int fillDeliveryId()
	 * @method \Bitrix\Main\Type\DateTime getDateInsert()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateInsert(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateInsert)
	 * @method bool hasDateInsert()
	 * @method bool isDateInsertFilled()
	 * @method bool isDateInsertChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateInsert()
	 * @method \Bitrix\Main\Type\DateTime requireDateInsert()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateInsert()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateInsert()
	 * @method \Bitrix\Main\Type\DateTime fillDateInsert()
	 * @method \Bitrix\Main\Type\DateTime getDateInsertShort()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateInsertShort()
	 * @method \Bitrix\Main\Type\DateTime requireDateInsertShort()
	 * @method bool hasDateInsertShort()
	 * @method bool isDateInsertShortFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateInsertShort()
	 * @method \Bitrix\Main\Type\DateTime fillDateInsertShort()
	 * @method \Bitrix\Main\Type\DateTime getDateInsertFormat()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateInsertFormat()
	 * @method \Bitrix\Main\Type\DateTime requireDateInsertFormat()
	 * @method bool hasDateInsertFormat()
	 * @method bool isDateInsertFormatFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateInsertFormat()
	 * @method \Bitrix\Main\Type\DateTime fillDateInsertFormat()
	 * @method \Bitrix\Main\Type\DateTime getDateUpdate()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateUpdate(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateUpdate)
	 * @method bool hasDateUpdate()
	 * @method bool isDateUpdateFilled()
	 * @method bool isDateUpdateChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateUpdate()
	 * @method \Bitrix\Main\Type\DateTime requireDateUpdate()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateUpdate()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateUpdate()
	 * @method \Bitrix\Main\Type\DateTime fillDateUpdate()
	 * @method \Bitrix\Main\Type\DateTime getDateUpdateShort()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateUpdateShort()
	 * @method \Bitrix\Main\Type\DateTime requireDateUpdateShort()
	 * @method bool hasDateUpdateShort()
	 * @method bool isDateUpdateShortFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateUpdateShort()
	 * @method \Bitrix\Main\Type\DateTime fillDateUpdateShort()
	 * @method \string getProductsQuant()
	 * @method \string remindActualProductsQuant()
	 * @method \string requireProductsQuant()
	 * @method bool hasProductsQuant()
	 * @method bool isProductsQuantFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetProductsQuant()
	 * @method \string fillProductsQuant()
	 * @method \string getPersonTypeId()
	 * @method \Bitrix\Sale\Internals\EO_Order setPersonTypeId(\string|\Bitrix\Main\DB\SqlExpression $personTypeId)
	 * @method bool hasPersonTypeId()
	 * @method bool isPersonTypeIdFilled()
	 * @method bool isPersonTypeIdChanged()
	 * @method \string remindActualPersonTypeId()
	 * @method \string requirePersonTypeId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetPersonTypeId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetPersonTypeId()
	 * @method \string fillPersonTypeId()
	 * @method \int getUserId()
	 * @method \Bitrix\Sale\Internals\EO_Order setUserId(\int|\Bitrix\Main\DB\SqlExpression $userId)
	 * @method bool hasUserId()
	 * @method bool isUserIdFilled()
	 * @method bool isUserIdChanged()
	 * @method \int remindActualUserId()
	 * @method \int requireUserId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetUserId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetUserId()
	 * @method \int fillUserId()
	 * @method \Bitrix\Main\EO_User getUser()
	 * @method \Bitrix\Main\EO_User remindActualUser()
	 * @method \Bitrix\Main\EO_User requireUser()
	 * @method \Bitrix\Sale\Internals\EO_Order setUser(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetUser()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetUser()
	 * @method bool hasUser()
	 * @method bool isUserFilled()
	 * @method bool isUserChanged()
	 * @method \Bitrix\Main\EO_User fillUser()
	 * @method \boolean getPayed()
	 * @method \Bitrix\Sale\Internals\EO_Order setPayed(\boolean|\Bitrix\Main\DB\SqlExpression $payed)
	 * @method bool hasPayed()
	 * @method bool isPayedFilled()
	 * @method bool isPayedChanged()
	 * @method \boolean remindActualPayed()
	 * @method \boolean requirePayed()
	 * @method \Bitrix\Sale\Internals\EO_Order resetPayed()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetPayed()
	 * @method \boolean fillPayed()
	 * @method \boolean getIsSyncB24()
	 * @method \Bitrix\Sale\Internals\EO_Order setIsSyncB24(\boolean|\Bitrix\Main\DB\SqlExpression $isSyncB24)
	 * @method bool hasIsSyncB24()
	 * @method bool isIsSyncB24Filled()
	 * @method bool isIsSyncB24Changed()
	 * @method \boolean remindActualIsSyncB24()
	 * @method \boolean requireIsSyncB24()
	 * @method \Bitrix\Sale\Internals\EO_Order resetIsSyncB24()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetIsSyncB24()
	 * @method \boolean fillIsSyncB24()
	 * @method \Bitrix\Main\Type\DateTime getDatePayed()
	 * @method \Bitrix\Sale\Internals\EO_Order setDatePayed(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $datePayed)
	 * @method bool hasDatePayed()
	 * @method bool isDatePayedFilled()
	 * @method bool isDatePayedChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDatePayed()
	 * @method \Bitrix\Main\Type\DateTime requireDatePayed()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDatePayed()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDatePayed()
	 * @method \Bitrix\Main\Type\DateTime fillDatePayed()
	 * @method \int getEmpPayedId()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpPayedId(\int|\Bitrix\Main\DB\SqlExpression $empPayedId)
	 * @method bool hasEmpPayedId()
	 * @method bool isEmpPayedIdFilled()
	 * @method bool isEmpPayedIdChanged()
	 * @method \int remindActualEmpPayedId()
	 * @method \int requireEmpPayedId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpPayedId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpPayedId()
	 * @method \int fillEmpPayedId()
	 * @method \boolean getDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order setDeducted(\boolean|\Bitrix\Main\DB\SqlExpression $deducted)
	 * @method bool hasDeducted()
	 * @method bool isDeductedFilled()
	 * @method bool isDeductedChanged()
	 * @method \boolean remindActualDeducted()
	 * @method \boolean requireDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDeducted()
	 * @method \boolean fillDeducted()
	 * @method \Bitrix\Main\Type\DateTime getDateDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateDeducted(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateDeducted)
	 * @method bool hasDateDeducted()
	 * @method bool isDateDeductedFilled()
	 * @method bool isDateDeductedChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateDeducted()
	 * @method \Bitrix\Main\Type\DateTime requireDateDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateDeducted()
	 * @method \Bitrix\Main\Type\DateTime fillDateDeducted()
	 * @method \int getEmpDeductedId()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpDeductedId(\int|\Bitrix\Main\DB\SqlExpression $empDeductedId)
	 * @method bool hasEmpDeductedId()
	 * @method bool isEmpDeductedIdFilled()
	 * @method bool isEmpDeductedIdChanged()
	 * @method \int remindActualEmpDeductedId()
	 * @method \int requireEmpDeductedId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpDeductedId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpDeductedId()
	 * @method \int fillEmpDeductedId()
	 * @method \string getReasonUndoDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order setReasonUndoDeducted(\string|\Bitrix\Main\DB\SqlExpression $reasonUndoDeducted)
	 * @method bool hasReasonUndoDeducted()
	 * @method bool isReasonUndoDeductedFilled()
	 * @method bool isReasonUndoDeductedChanged()
	 * @method \string remindActualReasonUndoDeducted()
	 * @method \string requireReasonUndoDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order resetReasonUndoDeducted()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetReasonUndoDeducted()
	 * @method \string fillReasonUndoDeducted()
	 * @method \string getStatusId()
	 * @method \Bitrix\Sale\Internals\EO_Order setStatusId(\string|\Bitrix\Main\DB\SqlExpression $statusId)
	 * @method bool hasStatusId()
	 * @method bool isStatusIdFilled()
	 * @method bool isStatusIdChanged()
	 * @method \string remindActualStatusId()
	 * @method \string requireStatusId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetStatusId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetStatusId()
	 * @method \string fillStatusId()
	 * @method \Bitrix\Sale\Internals\EO_StatusLang getStatus()
	 * @method \Bitrix\Sale\Internals\EO_StatusLang remindActualStatus()
	 * @method \Bitrix\Sale\Internals\EO_StatusLang requireStatus()
	 * @method \Bitrix\Sale\Internals\EO_Order setStatus(\Bitrix\Sale\Internals\EO_StatusLang $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetStatus()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetStatus()
	 * @method bool hasStatus()
	 * @method bool isStatusFilled()
	 * @method bool isStatusChanged()
	 * @method \Bitrix\Sale\Internals\EO_StatusLang fillStatus()
	 * @method \Bitrix\Main\Type\DateTime getDateStatus()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateStatus(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateStatus)
	 * @method bool hasDateStatus()
	 * @method bool isDateStatusFilled()
	 * @method bool isDateStatusChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateStatus()
	 * @method \Bitrix\Main\Type\DateTime requireDateStatus()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateStatus()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateStatus()
	 * @method \Bitrix\Main\Type\DateTime fillDateStatus()
	 * @method \Bitrix\Main\Type\DateTime getDateStatusShort()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateStatusShort()
	 * @method \Bitrix\Main\Type\DateTime requireDateStatusShort()
	 * @method bool hasDateStatusShort()
	 * @method bool isDateStatusShortFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateStatusShort()
	 * @method \Bitrix\Main\Type\DateTime fillDateStatusShort()
	 * @method \int getEmpStatusId()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpStatusId(\int|\Bitrix\Main\DB\SqlExpression $empStatusId)
	 * @method bool hasEmpStatusId()
	 * @method bool isEmpStatusIdFilled()
	 * @method bool isEmpStatusIdChanged()
	 * @method \int remindActualEmpStatusId()
	 * @method \int requireEmpStatusId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpStatusId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpStatusId()
	 * @method \int fillEmpStatusId()
	 * @method \Bitrix\Main\EO_User getEmpStatusBy()
	 * @method \Bitrix\Main\EO_User remindActualEmpStatusBy()
	 * @method \Bitrix\Main\EO_User requireEmpStatusBy()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpStatusBy(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpStatusBy()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpStatusBy()
	 * @method bool hasEmpStatusBy()
	 * @method bool isEmpStatusByFilled()
	 * @method bool isEmpStatusByChanged()
	 * @method \Bitrix\Main\EO_User fillEmpStatusBy()
	 * @method \boolean getMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order setMarked(\boolean|\Bitrix\Main\DB\SqlExpression $marked)
	 * @method bool hasMarked()
	 * @method bool isMarkedFilled()
	 * @method bool isMarkedChanged()
	 * @method \boolean remindActualMarked()
	 * @method \boolean requireMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order resetMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetMarked()
	 * @method \boolean fillMarked()
	 * @method \Bitrix\Main\Type\DateTime getDateMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateMarked(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateMarked)
	 * @method bool hasDateMarked()
	 * @method bool isDateMarkedFilled()
	 * @method bool isDateMarkedChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateMarked()
	 * @method \Bitrix\Main\Type\DateTime requireDateMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateMarked()
	 * @method \Bitrix\Main\Type\DateTime fillDateMarked()
	 * @method \int getEmpMarkedId()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpMarkedId(\int|\Bitrix\Main\DB\SqlExpression $empMarkedId)
	 * @method bool hasEmpMarkedId()
	 * @method bool isEmpMarkedIdFilled()
	 * @method bool isEmpMarkedIdChanged()
	 * @method \int remindActualEmpMarkedId()
	 * @method \int requireEmpMarkedId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpMarkedId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpMarkedId()
	 * @method \int fillEmpMarkedId()
	 * @method \Bitrix\Main\EO_User getEmpMarkedBy()
	 * @method \Bitrix\Main\EO_User remindActualEmpMarkedBy()
	 * @method \Bitrix\Main\EO_User requireEmpMarkedBy()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpMarkedBy(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpMarkedBy()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpMarkedBy()
	 * @method bool hasEmpMarkedBy()
	 * @method bool isEmpMarkedByFilled()
	 * @method bool isEmpMarkedByChanged()
	 * @method \Bitrix\Main\EO_User fillEmpMarkedBy()
	 * @method \string getReasonMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order setReasonMarked(\string|\Bitrix\Main\DB\SqlExpression $reasonMarked)
	 * @method bool hasReasonMarked()
	 * @method bool isReasonMarkedFilled()
	 * @method bool isReasonMarkedChanged()
	 * @method \string remindActualReasonMarked()
	 * @method \string requireReasonMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order resetReasonMarked()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetReasonMarked()
	 * @method \string fillReasonMarked()
	 * @method \float getPriceDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order setPriceDelivery(\float|\Bitrix\Main\DB\SqlExpression $priceDelivery)
	 * @method bool hasPriceDelivery()
	 * @method bool isPriceDeliveryFilled()
	 * @method bool isPriceDeliveryChanged()
	 * @method \float remindActualPriceDelivery()
	 * @method \float requirePriceDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order resetPriceDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetPriceDelivery()
	 * @method \float fillPriceDelivery()
	 * @method \boolean getAllowDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order setAllowDelivery(\boolean|\Bitrix\Main\DB\SqlExpression $allowDelivery)
	 * @method bool hasAllowDelivery()
	 * @method bool isAllowDeliveryFilled()
	 * @method bool isAllowDeliveryChanged()
	 * @method \boolean remindActualAllowDelivery()
	 * @method \boolean requireAllowDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order resetAllowDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetAllowDelivery()
	 * @method \boolean fillAllowDelivery()
	 * @method \Bitrix\Main\Type\DateTime getDateAllowDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateAllowDelivery(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateAllowDelivery)
	 * @method bool hasDateAllowDelivery()
	 * @method bool isDateAllowDeliveryFilled()
	 * @method bool isDateAllowDeliveryChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateAllowDelivery()
	 * @method \Bitrix\Main\Type\DateTime requireDateAllowDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateAllowDelivery()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateAllowDelivery()
	 * @method \Bitrix\Main\Type\DateTime fillDateAllowDelivery()
	 * @method \int getEmpAllowDeliveryId()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpAllowDeliveryId(\int|\Bitrix\Main\DB\SqlExpression $empAllowDeliveryId)
	 * @method bool hasEmpAllowDeliveryId()
	 * @method bool isEmpAllowDeliveryIdFilled()
	 * @method bool isEmpAllowDeliveryIdChanged()
	 * @method \int remindActualEmpAllowDeliveryId()
	 * @method \int requireEmpAllowDeliveryId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpAllowDeliveryId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpAllowDeliveryId()
	 * @method \int fillEmpAllowDeliveryId()
	 * @method \boolean getReserved()
	 * @method \Bitrix\Sale\Internals\EO_Order setReserved(\boolean|\Bitrix\Main\DB\SqlExpression $reserved)
	 * @method bool hasReserved()
	 * @method bool isReservedFilled()
	 * @method bool isReservedChanged()
	 * @method \boolean remindActualReserved()
	 * @method \boolean requireReserved()
	 * @method \Bitrix\Sale\Internals\EO_Order resetReserved()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetReserved()
	 * @method \boolean fillReserved()
	 * @method \float getPrice()
	 * @method \Bitrix\Sale\Internals\EO_Order setPrice(\float|\Bitrix\Main\DB\SqlExpression $price)
	 * @method bool hasPrice()
	 * @method bool isPriceFilled()
	 * @method bool isPriceChanged()
	 * @method \float remindActualPrice()
	 * @method \float requirePrice()
	 * @method \Bitrix\Sale\Internals\EO_Order resetPrice()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetPrice()
	 * @method \float fillPrice()
	 * @method \string getCurrency()
	 * @method \Bitrix\Sale\Internals\EO_Order setCurrency(\string|\Bitrix\Main\DB\SqlExpression $currency)
	 * @method bool hasCurrency()
	 * @method bool isCurrencyFilled()
	 * @method bool isCurrencyChanged()
	 * @method \string remindActualCurrency()
	 * @method \string requireCurrency()
	 * @method \Bitrix\Sale\Internals\EO_Order resetCurrency()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetCurrency()
	 * @method \string fillCurrency()
	 * @method \float getDiscountValue()
	 * @method \Bitrix\Sale\Internals\EO_Order setDiscountValue(\float|\Bitrix\Main\DB\SqlExpression $discountValue)
	 * @method bool hasDiscountValue()
	 * @method bool isDiscountValueFilled()
	 * @method bool isDiscountValueChanged()
	 * @method \float remindActualDiscountValue()
	 * @method \float requireDiscountValue()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDiscountValue()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDiscountValue()
	 * @method \float fillDiscountValue()
	 * @method \string getDiscountAll()
	 * @method \string remindActualDiscountAll()
	 * @method \string requireDiscountAll()
	 * @method bool hasDiscountAll()
	 * @method bool isDiscountAllFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDiscountAll()
	 * @method \string fillDiscountAll()
	 * @method \float getTaxValue()
	 * @method \Bitrix\Sale\Internals\EO_Order setTaxValue(\float|\Bitrix\Main\DB\SqlExpression $taxValue)
	 * @method bool hasTaxValue()
	 * @method bool isTaxValueFilled()
	 * @method bool isTaxValueChanged()
	 * @method \float remindActualTaxValue()
	 * @method \float requireTaxValue()
	 * @method \Bitrix\Sale\Internals\EO_Order resetTaxValue()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetTaxValue()
	 * @method \float fillTaxValue()
	 * @method \float getSumPaid()
	 * @method \Bitrix\Sale\Internals\EO_Order setSumPaid(\float|\Bitrix\Main\DB\SqlExpression $sumPaid)
	 * @method bool hasSumPaid()
	 * @method bool isSumPaidFilled()
	 * @method bool isSumPaidChanged()
	 * @method \float remindActualSumPaid()
	 * @method \float requireSumPaid()
	 * @method \Bitrix\Sale\Internals\EO_Order resetSumPaid()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetSumPaid()
	 * @method \float fillSumPaid()
	 * @method \string getSumPaidForrep()
	 * @method \string remindActualSumPaidForrep()
	 * @method \string requireSumPaidForrep()
	 * @method bool hasSumPaidForrep()
	 * @method bool isSumPaidForrepFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetSumPaidForrep()
	 * @method \string fillSumPaidForrep()
	 * @method \string getUserDescription()
	 * @method \Bitrix\Sale\Internals\EO_Order setUserDescription(\string|\Bitrix\Main\DB\SqlExpression $userDescription)
	 * @method bool hasUserDescription()
	 * @method bool isUserDescriptionFilled()
	 * @method bool isUserDescriptionChanged()
	 * @method \string remindActualUserDescription()
	 * @method \string requireUserDescription()
	 * @method \Bitrix\Sale\Internals\EO_Order resetUserDescription()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetUserDescription()
	 * @method \string fillUserDescription()
	 * @method \string getPayVoucherNum()
	 * @method \Bitrix\Sale\Internals\EO_Order setPayVoucherNum(\string|\Bitrix\Main\DB\SqlExpression $payVoucherNum)
	 * @method bool hasPayVoucherNum()
	 * @method bool isPayVoucherNumFilled()
	 * @method bool isPayVoucherNumChanged()
	 * @method \string remindActualPayVoucherNum()
	 * @method \string requirePayVoucherNum()
	 * @method \Bitrix\Sale\Internals\EO_Order resetPayVoucherNum()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetPayVoucherNum()
	 * @method \string fillPayVoucherNum()
	 * @method \Bitrix\Main\Type\Date getPayVoucherDate()
	 * @method \Bitrix\Sale\Internals\EO_Order setPayVoucherDate(\Bitrix\Main\Type\Date|\Bitrix\Main\DB\SqlExpression $payVoucherDate)
	 * @method bool hasPayVoucherDate()
	 * @method bool isPayVoucherDateFilled()
	 * @method bool isPayVoucherDateChanged()
	 * @method \Bitrix\Main\Type\Date remindActualPayVoucherDate()
	 * @method \Bitrix\Main\Type\Date requirePayVoucherDate()
	 * @method \Bitrix\Sale\Internals\EO_Order resetPayVoucherDate()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetPayVoucherDate()
	 * @method \Bitrix\Main\Type\Date fillPayVoucherDate()
	 * @method \string getAdditionalInfo()
	 * @method \Bitrix\Sale\Internals\EO_Order setAdditionalInfo(\string|\Bitrix\Main\DB\SqlExpression $additionalInfo)
	 * @method bool hasAdditionalInfo()
	 * @method bool isAdditionalInfoFilled()
	 * @method bool isAdditionalInfoChanged()
	 * @method \string remindActualAdditionalInfo()
	 * @method \string requireAdditionalInfo()
	 * @method \Bitrix\Sale\Internals\EO_Order resetAdditionalInfo()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetAdditionalInfo()
	 * @method \string fillAdditionalInfo()
	 * @method \string getComments()
	 * @method \Bitrix\Sale\Internals\EO_Order setComments(\string|\Bitrix\Main\DB\SqlExpression $comments)
	 * @method bool hasComments()
	 * @method bool isCommentsFilled()
	 * @method bool isCommentsChanged()
	 * @method \string remindActualComments()
	 * @method \string requireComments()
	 * @method \Bitrix\Sale\Internals\EO_Order resetComments()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetComments()
	 * @method \string fillComments()
	 * @method \int getCompanyId()
	 * @method \Bitrix\Sale\Internals\EO_Order setCompanyId(\int|\Bitrix\Main\DB\SqlExpression $companyId)
	 * @method bool hasCompanyId()
	 * @method bool isCompanyIdFilled()
	 * @method bool isCompanyIdChanged()
	 * @method \int remindActualCompanyId()
	 * @method \int requireCompanyId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetCompanyId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetCompanyId()
	 * @method \int fillCompanyId()
	 * @method \int getCreatedBy()
	 * @method \Bitrix\Sale\Internals\EO_Order setCreatedBy(\int|\Bitrix\Main\DB\SqlExpression $createdBy)
	 * @method bool hasCreatedBy()
	 * @method bool isCreatedByFilled()
	 * @method bool isCreatedByChanged()
	 * @method \int remindActualCreatedBy()
	 * @method \int requireCreatedBy()
	 * @method \Bitrix\Sale\Internals\EO_Order resetCreatedBy()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetCreatedBy()
	 * @method \int fillCreatedBy()
	 * @method \Bitrix\Main\EO_User getCreatedUser()
	 * @method \Bitrix\Main\EO_User remindActualCreatedUser()
	 * @method \Bitrix\Main\EO_User requireCreatedUser()
	 * @method \Bitrix\Sale\Internals\EO_Order setCreatedUser(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetCreatedUser()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetCreatedUser()
	 * @method bool hasCreatedUser()
	 * @method bool isCreatedUserFilled()
	 * @method bool isCreatedUserChanged()
	 * @method \Bitrix\Main\EO_User fillCreatedUser()
	 * @method \int getResponsibleId()
	 * @method \Bitrix\Sale\Internals\EO_Order setResponsibleId(\int|\Bitrix\Main\DB\SqlExpression $responsibleId)
	 * @method bool hasResponsibleId()
	 * @method bool isResponsibleIdFilled()
	 * @method bool isResponsibleIdChanged()
	 * @method \int remindActualResponsibleId()
	 * @method \int requireResponsibleId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetResponsibleId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetResponsibleId()
	 * @method \int fillResponsibleId()
	 * @method \Bitrix\Main\EO_User getResponsibleBy()
	 * @method \Bitrix\Main\EO_User remindActualResponsibleBy()
	 * @method \Bitrix\Main\EO_User requireResponsibleBy()
	 * @method \Bitrix\Sale\Internals\EO_Order setResponsibleBy(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetResponsibleBy()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetResponsibleBy()
	 * @method bool hasResponsibleBy()
	 * @method bool isResponsibleByFilled()
	 * @method bool isResponsibleByChanged()
	 * @method \Bitrix\Main\EO_User fillResponsibleBy()
	 * @method \string getStatGid()
	 * @method \Bitrix\Sale\Internals\EO_Order setStatGid(\string|\Bitrix\Main\DB\SqlExpression $statGid)
	 * @method bool hasStatGid()
	 * @method bool isStatGidFilled()
	 * @method bool isStatGidChanged()
	 * @method \string remindActualStatGid()
	 * @method \string requireStatGid()
	 * @method \Bitrix\Sale\Internals\EO_Order resetStatGid()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetStatGid()
	 * @method \string fillStatGid()
	 * @method \Bitrix\Main\Type\Date getDatePayBefore()
	 * @method \Bitrix\Sale\Internals\EO_Order setDatePayBefore(\Bitrix\Main\Type\Date|\Bitrix\Main\DB\SqlExpression $datePayBefore)
	 * @method bool hasDatePayBefore()
	 * @method bool isDatePayBeforeFilled()
	 * @method bool isDatePayBeforeChanged()
	 * @method \Bitrix\Main\Type\Date remindActualDatePayBefore()
	 * @method \Bitrix\Main\Type\Date requireDatePayBefore()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDatePayBefore()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDatePayBefore()
	 * @method \Bitrix\Main\Type\Date fillDatePayBefore()
	 * @method \Bitrix\Main\Type\Date getDateBill()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateBill(\Bitrix\Main\Type\Date|\Bitrix\Main\DB\SqlExpression $dateBill)
	 * @method bool hasDateBill()
	 * @method bool isDateBillFilled()
	 * @method bool isDateBillChanged()
	 * @method \Bitrix\Main\Type\Date remindActualDateBill()
	 * @method \Bitrix\Main\Type\Date requireDateBill()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateBill()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateBill()
	 * @method \Bitrix\Main\Type\Date fillDateBill()
	 * @method \boolean getIsRecurring()
	 * @method \Bitrix\Sale\Internals\EO_Order setIsRecurring(\boolean|\Bitrix\Main\DB\SqlExpression $isRecurring)
	 * @method bool hasIsRecurring()
	 * @method bool isIsRecurringFilled()
	 * @method bool isIsRecurringChanged()
	 * @method \boolean remindActualIsRecurring()
	 * @method \boolean requireIsRecurring()
	 * @method \Bitrix\Sale\Internals\EO_Order resetIsRecurring()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetIsRecurring()
	 * @method \boolean fillIsRecurring()
	 * @method \int getRecurringId()
	 * @method \Bitrix\Sale\Internals\EO_Order setRecurringId(\int|\Bitrix\Main\DB\SqlExpression $recurringId)
	 * @method bool hasRecurringId()
	 * @method bool isRecurringIdFilled()
	 * @method bool isRecurringIdChanged()
	 * @method \int remindActualRecurringId()
	 * @method \int requireRecurringId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetRecurringId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetRecurringId()
	 * @method \int fillRecurringId()
	 * @method \int getLockedBy()
	 * @method \Bitrix\Sale\Internals\EO_Order setLockedBy(\int|\Bitrix\Main\DB\SqlExpression $lockedBy)
	 * @method bool hasLockedBy()
	 * @method bool isLockedByFilled()
	 * @method bool isLockedByChanged()
	 * @method \int remindActualLockedBy()
	 * @method \int requireLockedBy()
	 * @method \Bitrix\Sale\Internals\EO_Order resetLockedBy()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetLockedBy()
	 * @method \int fillLockedBy()
	 * @method \Bitrix\Main\EO_User getLockUser()
	 * @method \Bitrix\Main\EO_User remindActualLockUser()
	 * @method \Bitrix\Main\EO_User requireLockUser()
	 * @method \Bitrix\Sale\Internals\EO_Order setLockUser(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetLockUser()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetLockUser()
	 * @method bool hasLockUser()
	 * @method bool isLockUserFilled()
	 * @method bool isLockUserChanged()
	 * @method \Bitrix\Main\EO_User fillLockUser()
	 * @method \Bitrix\Main\Type\DateTime getDateLock()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateLock(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateLock)
	 * @method bool hasDateLock()
	 * @method bool isDateLockFilled()
	 * @method bool isDateLockChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateLock()
	 * @method \Bitrix\Main\Type\DateTime requireDateLock()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateLock()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateLock()
	 * @method \Bitrix\Main\Type\DateTime fillDateLock()
	 * @method \string getLockUserName()
	 * @method \string remindActualLockUserName()
	 * @method \string requireLockUserName()
	 * @method bool hasLockUserName()
	 * @method bool isLockUserNameFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetLockUserName()
	 * @method \string fillLockUserName()
	 * @method \string getLockStatus()
	 * @method \string remindActualLockStatus()
	 * @method \string requireLockStatus()
	 * @method bool hasLockStatus()
	 * @method bool isLockStatusFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetLockStatus()
	 * @method \string fillLockStatus()
	 * @method \Bitrix\Main\EO_UserGroup getUserGroup()
	 * @method \Bitrix\Main\EO_UserGroup remindActualUserGroup()
	 * @method \Bitrix\Main\EO_UserGroup requireUserGroup()
	 * @method \Bitrix\Sale\Internals\EO_Order setUserGroup(\Bitrix\Main\EO_UserGroup $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetUserGroup()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetUserGroup()
	 * @method bool hasUserGroup()
	 * @method bool isUserGroupFilled()
	 * @method bool isUserGroupChanged()
	 * @method \Bitrix\Main\EO_UserGroup fillUserGroup()
	 * @method \Bitrix\Main\EO_User getResponsible()
	 * @method \Bitrix\Main\EO_User remindActualResponsible()
	 * @method \Bitrix\Main\EO_User requireResponsible()
	 * @method \Bitrix\Sale\Internals\EO_Order setResponsible(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetResponsible()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetResponsible()
	 * @method bool hasResponsible()
	 * @method bool isResponsibleFilled()
	 * @method bool isResponsibleChanged()
	 * @method \Bitrix\Main\EO_User fillResponsible()
	 * @method \Bitrix\Sale\Internals\EO_Basket getBasket()
	 * @method \Bitrix\Sale\Internals\EO_Basket remindActualBasket()
	 * @method \Bitrix\Sale\Internals\EO_Basket requireBasket()
	 * @method \Bitrix\Sale\Internals\EO_Order setBasket(\Bitrix\Sale\Internals\EO_Basket $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetBasket()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetBasket()
	 * @method bool hasBasket()
	 * @method bool isBasketFilled()
	 * @method bool isBasketChanged()
	 * @method \Bitrix\Sale\Internals\EO_Basket fillBasket()
	 * @method \string getBasketPriceTotal()
	 * @method \string remindActualBasketPriceTotal()
	 * @method \string requireBasketPriceTotal()
	 * @method bool hasBasketPriceTotal()
	 * @method bool isBasketPriceTotalFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetBasketPriceTotal()
	 * @method \string fillBasketPriceTotal()
	 * @method \Bitrix\Sale\Internals\EO_Payment getPayment()
	 * @method \Bitrix\Sale\Internals\EO_Payment remindActualPayment()
	 * @method \Bitrix\Sale\Internals\EO_Payment requirePayment()
	 * @method \Bitrix\Sale\Internals\EO_Order setPayment(\Bitrix\Sale\Internals\EO_Payment $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetPayment()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetPayment()
	 * @method bool hasPayment()
	 * @method bool isPaymentFilled()
	 * @method bool isPaymentChanged()
	 * @method \Bitrix\Sale\Internals\EO_Payment fillPayment()
	 * @method \Bitrix\Sale\Internals\EO_Shipment getShipment()
	 * @method \Bitrix\Sale\Internals\EO_Shipment remindActualShipment()
	 * @method \Bitrix\Sale\Internals\EO_Shipment requireShipment()
	 * @method \Bitrix\Sale\Internals\EO_Order setShipment(\Bitrix\Sale\Internals\EO_Shipment $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetShipment()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetShipment()
	 * @method bool hasShipment()
	 * @method bool isShipmentFilled()
	 * @method bool isShipmentChanged()
	 * @method \Bitrix\Sale\Internals\EO_Shipment fillShipment()
	 * @method \Bitrix\Sale\Internals\EO_OrderPropsValue getProperty()
	 * @method \Bitrix\Sale\Internals\EO_OrderPropsValue remindActualProperty()
	 * @method \Bitrix\Sale\Internals\EO_OrderPropsValue requireProperty()
	 * @method \Bitrix\Sale\Internals\EO_Order setProperty(\Bitrix\Sale\Internals\EO_OrderPropsValue $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetProperty()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetProperty()
	 * @method bool hasProperty()
	 * @method bool isPropertyFilled()
	 * @method bool isPropertyChanged()
	 * @method \Bitrix\Sale\Internals\EO_OrderPropsValue fillProperty()
	 * @method \boolean getRecountFlag()
	 * @method \Bitrix\Sale\Internals\EO_Order setRecountFlag(\boolean|\Bitrix\Main\DB\SqlExpression $recountFlag)
	 * @method bool hasRecountFlag()
	 * @method bool isRecountFlagFilled()
	 * @method bool isRecountFlagChanged()
	 * @method \boolean remindActualRecountFlag()
	 * @method \boolean requireRecountFlag()
	 * @method \Bitrix\Sale\Internals\EO_Order resetRecountFlag()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetRecountFlag()
	 * @method \boolean fillRecountFlag()
	 * @method \int getAffiliateId()
	 * @method \Bitrix\Sale\Internals\EO_Order setAffiliateId(\int|\Bitrix\Main\DB\SqlExpression $affiliateId)
	 * @method bool hasAffiliateId()
	 * @method bool isAffiliateIdFilled()
	 * @method bool isAffiliateIdChanged()
	 * @method \int remindActualAffiliateId()
	 * @method \int requireAffiliateId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetAffiliateId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetAffiliateId()
	 * @method \int fillAffiliateId()
	 * @method \string getDeliveryDocNum()
	 * @method \Bitrix\Sale\Internals\EO_Order setDeliveryDocNum(\string|\Bitrix\Main\DB\SqlExpression $deliveryDocNum)
	 * @method bool hasDeliveryDocNum()
	 * @method bool isDeliveryDocNumFilled()
	 * @method bool isDeliveryDocNumChanged()
	 * @method \string remindActualDeliveryDocNum()
	 * @method \string requireDeliveryDocNum()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDeliveryDocNum()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDeliveryDocNum()
	 * @method \string fillDeliveryDocNum()
	 * @method \Bitrix\Main\Type\DateTime getDeliveryDocDate()
	 * @method \Bitrix\Sale\Internals\EO_Order setDeliveryDocDate(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $deliveryDocDate)
	 * @method bool hasDeliveryDocDate()
	 * @method bool isDeliveryDocDateFilled()
	 * @method bool isDeliveryDocDateChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDeliveryDocDate()
	 * @method \Bitrix\Main\Type\DateTime requireDeliveryDocDate()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDeliveryDocDate()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDeliveryDocDate()
	 * @method \Bitrix\Main\Type\DateTime fillDeliveryDocDate()
	 * @method \boolean getUpdated1c()
	 * @method \Bitrix\Sale\Internals\EO_Order setUpdated1c(\boolean|\Bitrix\Main\DB\SqlExpression $updated1c)
	 * @method bool hasUpdated1c()
	 * @method bool isUpdated1cFilled()
	 * @method bool isUpdated1cChanged()
	 * @method \boolean remindActualUpdated1c()
	 * @method \boolean requireUpdated1c()
	 * @method \Bitrix\Sale\Internals\EO_Order resetUpdated1c()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetUpdated1c()
	 * @method \boolean fillUpdated1c()
	 * @method \string getOrderTopic()
	 * @method \Bitrix\Sale\Internals\EO_Order setOrderTopic(\string|\Bitrix\Main\DB\SqlExpression $orderTopic)
	 * @method bool hasOrderTopic()
	 * @method bool isOrderTopicFilled()
	 * @method bool isOrderTopicChanged()
	 * @method \string remindActualOrderTopic()
	 * @method \string requireOrderTopic()
	 * @method \Bitrix\Sale\Internals\EO_Order resetOrderTopic()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetOrderTopic()
	 * @method \string fillOrderTopic()
	 * @method \string getXmlId()
	 * @method \Bitrix\Sale\Internals\EO_Order setXmlId(\string|\Bitrix\Main\DB\SqlExpression $xmlId)
	 * @method bool hasXmlId()
	 * @method bool isXmlIdFilled()
	 * @method bool isXmlIdChanged()
	 * @method \string remindActualXmlId()
	 * @method \string requireXmlId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetXmlId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetXmlId()
	 * @method \string fillXmlId()
	 * @method \string getId1c()
	 * @method \Bitrix\Sale\Internals\EO_Order setId1c(\string|\Bitrix\Main\DB\SqlExpression $id1c)
	 * @method bool hasId1c()
	 * @method bool isId1cFilled()
	 * @method bool isId1cChanged()
	 * @method \string remindActualId1c()
	 * @method \string requireId1c()
	 * @method \Bitrix\Sale\Internals\EO_Order resetId1c()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetId1c()
	 * @method \string fillId1c()
	 * @method \string getVersion1c()
	 * @method \Bitrix\Sale\Internals\EO_Order setVersion1c(\string|\Bitrix\Main\DB\SqlExpression $version1c)
	 * @method bool hasVersion1c()
	 * @method bool isVersion1cFilled()
	 * @method bool isVersion1cChanged()
	 * @method \string remindActualVersion1c()
	 * @method \string requireVersion1c()
	 * @method \Bitrix\Sale\Internals\EO_Order resetVersion1c()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetVersion1c()
	 * @method \string fillVersion1c()
	 * @method \int getVersion()
	 * @method \Bitrix\Sale\Internals\EO_Order setVersion(\int|\Bitrix\Main\DB\SqlExpression $version)
	 * @method bool hasVersion()
	 * @method bool isVersionFilled()
	 * @method bool isVersionChanged()
	 * @method \int remindActualVersion()
	 * @method \int requireVersion()
	 * @method \Bitrix\Sale\Internals\EO_Order resetVersion()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetVersion()
	 * @method \int fillVersion()
	 * @method \boolean getExternalOrder()
	 * @method \Bitrix\Sale\Internals\EO_Order setExternalOrder(\boolean|\Bitrix\Main\DB\SqlExpression $externalOrder)
	 * @method bool hasExternalOrder()
	 * @method bool isExternalOrderFilled()
	 * @method bool isExternalOrderChanged()
	 * @method \boolean remindActualExternalOrder()
	 * @method \boolean requireExternalOrder()
	 * @method \Bitrix\Sale\Internals\EO_Order resetExternalOrder()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetExternalOrder()
	 * @method \boolean fillExternalOrder()
	 * @method \int getStoreId()
	 * @method \Bitrix\Sale\Internals\EO_Order setStoreId(\int|\Bitrix\Main\DB\SqlExpression $storeId)
	 * @method bool hasStoreId()
	 * @method bool isStoreIdFilled()
	 * @method bool isStoreIdChanged()
	 * @method \int remindActualStoreId()
	 * @method \int requireStoreId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetStoreId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetStoreId()
	 * @method \int fillStoreId()
	 * @method \boolean getCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order setCanceled(\boolean|\Bitrix\Main\DB\SqlExpression $canceled)
	 * @method bool hasCanceled()
	 * @method bool isCanceledFilled()
	 * @method bool isCanceledChanged()
	 * @method \boolean remindActualCanceled()
	 * @method \boolean requireCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order resetCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetCanceled()
	 * @method \boolean fillCanceled()
	 * @method \int getEmpCanceledId()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpCanceledId(\int|\Bitrix\Main\DB\SqlExpression $empCanceledId)
	 * @method bool hasEmpCanceledId()
	 * @method bool isEmpCanceledIdFilled()
	 * @method bool isEmpCanceledIdChanged()
	 * @method \int remindActualEmpCanceledId()
	 * @method \int requireEmpCanceledId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpCanceledId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpCanceledId()
	 * @method \int fillEmpCanceledId()
	 * @method \Bitrix\Main\EO_User getEmpCanceledBy()
	 * @method \Bitrix\Main\EO_User remindActualEmpCanceledBy()
	 * @method \Bitrix\Main\EO_User requireEmpCanceledBy()
	 * @method \Bitrix\Sale\Internals\EO_Order setEmpCanceledBy(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetEmpCanceledBy()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetEmpCanceledBy()
	 * @method bool hasEmpCanceledBy()
	 * @method bool isEmpCanceledByFilled()
	 * @method bool isEmpCanceledByChanged()
	 * @method \Bitrix\Main\EO_User fillEmpCanceledBy()
	 * @method \Bitrix\Main\Type\DateTime getDateCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order setDateCanceled(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateCanceled)
	 * @method bool hasDateCanceled()
	 * @method bool isDateCanceledFilled()
	 * @method bool isDateCanceledChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateCanceled()
	 * @method \Bitrix\Main\Type\DateTime requireDateCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order resetDateCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateCanceled()
	 * @method \Bitrix\Main\Type\DateTime fillDateCanceled()
	 * @method \string getDateCanceledShort()
	 * @method \string remindActualDateCanceledShort()
	 * @method \string requireDateCanceledShort()
	 * @method bool hasDateCanceledShort()
	 * @method bool isDateCanceledShortFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetDateCanceledShort()
	 * @method \string fillDateCanceledShort()
	 * @method \string getReasonCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order setReasonCanceled(\string|\Bitrix\Main\DB\SqlExpression $reasonCanceled)
	 * @method bool hasReasonCanceled()
	 * @method bool isReasonCanceledFilled()
	 * @method bool isReasonCanceledChanged()
	 * @method \string remindActualReasonCanceled()
	 * @method \string requireReasonCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order resetReasonCanceled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetReasonCanceled()
	 * @method \string fillReasonCanceled()
	 * @method \string getBxUserId()
	 * @method \Bitrix\Sale\Internals\EO_Order setBxUserId(\string|\Bitrix\Main\DB\SqlExpression $bxUserId)
	 * @method bool hasBxUserId()
	 * @method bool isBxUserIdFilled()
	 * @method bool isBxUserIdChanged()
	 * @method \string remindActualBxUserId()
	 * @method \string requireBxUserId()
	 * @method \Bitrix\Sale\Internals\EO_Order resetBxUserId()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetBxUserId()
	 * @method \string fillBxUserId()
	 * @method \string getSearchContent()
	 * @method \Bitrix\Sale\Internals\EO_Order setSearchContent(\string|\Bitrix\Main\DB\SqlExpression $searchContent)
	 * @method bool hasSearchContent()
	 * @method bool isSearchContentFilled()
	 * @method bool isSearchContentChanged()
	 * @method \string remindActualSearchContent()
	 * @method \string requireSearchContent()
	 * @method \Bitrix\Sale\Internals\EO_Order resetSearchContent()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetSearchContent()
	 * @method \string fillSearchContent()
	 * @method \boolean getRunning()
	 * @method \Bitrix\Sale\Internals\EO_Order setRunning(\boolean|\Bitrix\Main\DB\SqlExpression $running)
	 * @method bool hasRunning()
	 * @method bool isRunningFilled()
	 * @method bool isRunningChanged()
	 * @method \boolean remindActualRunning()
	 * @method \boolean requireRunning()
	 * @method \Bitrix\Sale\Internals\EO_Order resetRunning()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetRunning()
	 * @method \boolean fillRunning()
	 * @method \Bitrix\Sale\Internals\EO_OrderCoupons getOrderCoupons()
	 * @method \Bitrix\Sale\Internals\EO_OrderCoupons remindActualOrderCoupons()
	 * @method \Bitrix\Sale\Internals\EO_OrderCoupons requireOrderCoupons()
	 * @method \Bitrix\Sale\Internals\EO_Order setOrderCoupons(\Bitrix\Sale\Internals\EO_OrderCoupons $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetOrderCoupons()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetOrderCoupons()
	 * @method bool hasOrderCoupons()
	 * @method bool isOrderCouponsFilled()
	 * @method bool isOrderCouponsChanged()
	 * @method \Bitrix\Sale\Internals\EO_OrderCoupons fillOrderCoupons()
	 * @method \Bitrix\Sale\Internals\EO_OrderDiscountData getOrderDiscountData()
	 * @method \Bitrix\Sale\Internals\EO_OrderDiscountData remindActualOrderDiscountData()
	 * @method \Bitrix\Sale\Internals\EO_OrderDiscountData requireOrderDiscountData()
	 * @method \Bitrix\Sale\Internals\EO_Order setOrderDiscountData(\Bitrix\Sale\Internals\EO_OrderDiscountData $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetOrderDiscountData()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetOrderDiscountData()
	 * @method bool hasOrderDiscountData()
	 * @method bool isOrderDiscountDataFilled()
	 * @method bool isOrderDiscountDataChanged()
	 * @method \Bitrix\Sale\Internals\EO_OrderDiscountData fillOrderDiscountData()
	 * @method \Bitrix\Sale\Internals\EO_OrderRules getOrderDiscountRules()
	 * @method \Bitrix\Sale\Internals\EO_OrderRules remindActualOrderDiscountRules()
	 * @method \Bitrix\Sale\Internals\EO_OrderRules requireOrderDiscountRules()
	 * @method \Bitrix\Sale\Internals\EO_Order setOrderDiscountRules(\Bitrix\Sale\Internals\EO_OrderRules $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetOrderDiscountRules()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetOrderDiscountRules()
	 * @method bool hasOrderDiscountRules()
	 * @method bool isOrderDiscountRulesFilled()
	 * @method bool isOrderDiscountRulesChanged()
	 * @method \Bitrix\Sale\Internals\EO_OrderRules fillOrderDiscountRules()
	 * @method \string getByRecommendation()
	 * @method \string remindActualByRecommendation()
	 * @method \string requireByRecommendation()
	 * @method bool hasByRecommendation()
	 * @method bool isByRecommendationFilled()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetByRecommendation()
	 * @method \string fillByRecommendation()
	 * @method \Bitrix\Sale\TradingPlatform\EO_Order getTradingPlatform()
	 * @method \Bitrix\Sale\TradingPlatform\EO_Order remindActualTradingPlatform()
	 * @method \Bitrix\Sale\TradingPlatform\EO_Order requireTradingPlatform()
	 * @method \Bitrix\Sale\Internals\EO_Order setTradingPlatform(\Bitrix\Sale\TradingPlatform\EO_Order $object)
	 * @method \Bitrix\Sale\Internals\EO_Order resetTradingPlatform()
	 * @method \Bitrix\Sale\Internals\EO_Order unsetTradingPlatform()
	 * @method bool hasTradingPlatform()
	 * @method bool isTradingPlatformFilled()
	 * @method bool isTradingPlatformChanged()
	 * @method \Bitrix\Sale\TradingPlatform\EO_Order fillTradingPlatform()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Order set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_Order reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Order unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_Order wakeUp($data)
	 */
	class EO_Order {
		/* @var \Bitrix\Sale\Internals\OrderTable */
		static public $dataClass = '\Bitrix\Sale\Internals\OrderTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Order_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \string[] getLidList()
	 * @method \string[] fillLid()
	 * @method \string[] getAccountNumberList()
	 * @method \string[] fillAccountNumber()
	 * @method \string[] getTrackingNumberList()
	 * @method \string[] fillTrackingNumber()
	 * @method \int[] getPaySystemIdList()
	 * @method \int[] fillPaySystemId()
	 * @method \int[] getDeliveryIdList()
	 * @method \int[] fillDeliveryId()
	 * @method \Bitrix\Main\Type\DateTime[] getDateInsertList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateInsert()
	 * @method \Bitrix\Main\Type\DateTime[] getDateInsertShortList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateInsertShort()
	 * @method \Bitrix\Main\Type\DateTime[] getDateInsertFormatList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateInsertFormat()
	 * @method \Bitrix\Main\Type\DateTime[] getDateUpdateList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateUpdate()
	 * @method \Bitrix\Main\Type\DateTime[] getDateUpdateShortList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateUpdateShort()
	 * @method \string[] getProductsQuantList()
	 * @method \string[] fillProductsQuant()
	 * @method \string[] getPersonTypeIdList()
	 * @method \string[] fillPersonTypeId()
	 * @method \int[] getUserIdList()
	 * @method \int[] fillUserId()
	 * @method \Bitrix\Main\EO_User[] getUserList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getUserCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillUser()
	 * @method \boolean[] getPayedList()
	 * @method \boolean[] fillPayed()
	 * @method \boolean[] getIsSyncB24List()
	 * @method \boolean[] fillIsSyncB24()
	 * @method \Bitrix\Main\Type\DateTime[] getDatePayedList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDatePayed()
	 * @method \int[] getEmpPayedIdList()
	 * @method \int[] fillEmpPayedId()
	 * @method \boolean[] getDeductedList()
	 * @method \boolean[] fillDeducted()
	 * @method \Bitrix\Main\Type\DateTime[] getDateDeductedList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateDeducted()
	 * @method \int[] getEmpDeductedIdList()
	 * @method \int[] fillEmpDeductedId()
	 * @method \string[] getReasonUndoDeductedList()
	 * @method \string[] fillReasonUndoDeducted()
	 * @method \string[] getStatusIdList()
	 * @method \string[] fillStatusId()
	 * @method \Bitrix\Sale\Internals\EO_StatusLang[] getStatusList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getStatusCollection()
	 * @method \Bitrix\Sale\Internals\EO_StatusLang_Collection fillStatus()
	 * @method \Bitrix\Main\Type\DateTime[] getDateStatusList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateStatus()
	 * @method \Bitrix\Main\Type\DateTime[] getDateStatusShortList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateStatusShort()
	 * @method \int[] getEmpStatusIdList()
	 * @method \int[] fillEmpStatusId()
	 * @method \Bitrix\Main\EO_User[] getEmpStatusByList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getEmpStatusByCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillEmpStatusBy()
	 * @method \boolean[] getMarkedList()
	 * @method \boolean[] fillMarked()
	 * @method \Bitrix\Main\Type\DateTime[] getDateMarkedList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateMarked()
	 * @method \int[] getEmpMarkedIdList()
	 * @method \int[] fillEmpMarkedId()
	 * @method \Bitrix\Main\EO_User[] getEmpMarkedByList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getEmpMarkedByCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillEmpMarkedBy()
	 * @method \string[] getReasonMarkedList()
	 * @method \string[] fillReasonMarked()
	 * @method \float[] getPriceDeliveryList()
	 * @method \float[] fillPriceDelivery()
	 * @method \boolean[] getAllowDeliveryList()
	 * @method \boolean[] fillAllowDelivery()
	 * @method \Bitrix\Main\Type\DateTime[] getDateAllowDeliveryList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateAllowDelivery()
	 * @method \int[] getEmpAllowDeliveryIdList()
	 * @method \int[] fillEmpAllowDeliveryId()
	 * @method \boolean[] getReservedList()
	 * @method \boolean[] fillReserved()
	 * @method \float[] getPriceList()
	 * @method \float[] fillPrice()
	 * @method \string[] getCurrencyList()
	 * @method \string[] fillCurrency()
	 * @method \float[] getDiscountValueList()
	 * @method \float[] fillDiscountValue()
	 * @method \string[] getDiscountAllList()
	 * @method \string[] fillDiscountAll()
	 * @method \float[] getTaxValueList()
	 * @method \float[] fillTaxValue()
	 * @method \float[] getSumPaidList()
	 * @method \float[] fillSumPaid()
	 * @method \string[] getSumPaidForrepList()
	 * @method \string[] fillSumPaidForrep()
	 * @method \string[] getUserDescriptionList()
	 * @method \string[] fillUserDescription()
	 * @method \string[] getPayVoucherNumList()
	 * @method \string[] fillPayVoucherNum()
	 * @method \Bitrix\Main\Type\Date[] getPayVoucherDateList()
	 * @method \Bitrix\Main\Type\Date[] fillPayVoucherDate()
	 * @method \string[] getAdditionalInfoList()
	 * @method \string[] fillAdditionalInfo()
	 * @method \string[] getCommentsList()
	 * @method \string[] fillComments()
	 * @method \int[] getCompanyIdList()
	 * @method \int[] fillCompanyId()
	 * @method \int[] getCreatedByList()
	 * @method \int[] fillCreatedBy()
	 * @method \Bitrix\Main\EO_User[] getCreatedUserList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getCreatedUserCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillCreatedUser()
	 * @method \int[] getResponsibleIdList()
	 * @method \int[] fillResponsibleId()
	 * @method \Bitrix\Main\EO_User[] getResponsibleByList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getResponsibleByCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillResponsibleBy()
	 * @method \string[] getStatGidList()
	 * @method \string[] fillStatGid()
	 * @method \Bitrix\Main\Type\Date[] getDatePayBeforeList()
	 * @method \Bitrix\Main\Type\Date[] fillDatePayBefore()
	 * @method \Bitrix\Main\Type\Date[] getDateBillList()
	 * @method \Bitrix\Main\Type\Date[] fillDateBill()
	 * @method \boolean[] getIsRecurringList()
	 * @method \boolean[] fillIsRecurring()
	 * @method \int[] getRecurringIdList()
	 * @method \int[] fillRecurringId()
	 * @method \int[] getLockedByList()
	 * @method \int[] fillLockedBy()
	 * @method \Bitrix\Main\EO_User[] getLockUserList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getLockUserCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillLockUser()
	 * @method \Bitrix\Main\Type\DateTime[] getDateLockList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateLock()
	 * @method \string[] getLockUserNameList()
	 * @method \string[] fillLockUserName()
	 * @method \string[] getLockStatusList()
	 * @method \string[] fillLockStatus()
	 * @method \Bitrix\Main\EO_UserGroup[] getUserGroupList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getUserGroupCollection()
	 * @method \Bitrix\Main\EO_UserGroup_Collection fillUserGroup()
	 * @method \Bitrix\Main\EO_User[] getResponsibleList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getResponsibleCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillResponsible()
	 * @method \Bitrix\Sale\Internals\EO_Basket[] getBasketList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getBasketCollection()
	 * @method \Bitrix\Sale\Internals\EO_Basket_Collection fillBasket()
	 * @method \string[] getBasketPriceTotalList()
	 * @method \string[] fillBasketPriceTotal()
	 * @method \Bitrix\Sale\Internals\EO_Payment[] getPaymentList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getPaymentCollection()
	 * @method \Bitrix\Sale\Internals\EO_Payment_Collection fillPayment()
	 * @method \Bitrix\Sale\Internals\EO_Shipment[] getShipmentList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getShipmentCollection()
	 * @method \Bitrix\Sale\Internals\EO_Shipment_Collection fillShipment()
	 * @method \Bitrix\Sale\Internals\EO_OrderPropsValue[] getPropertyList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getPropertyCollection()
	 * @method \Bitrix\Sale\Internals\EO_OrderPropsValue_Collection fillProperty()
	 * @method \boolean[] getRecountFlagList()
	 * @method \boolean[] fillRecountFlag()
	 * @method \int[] getAffiliateIdList()
	 * @method \int[] fillAffiliateId()
	 * @method \string[] getDeliveryDocNumList()
	 * @method \string[] fillDeliveryDocNum()
	 * @method \Bitrix\Main\Type\DateTime[] getDeliveryDocDateList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDeliveryDocDate()
	 * @method \boolean[] getUpdated1cList()
	 * @method \boolean[] fillUpdated1c()
	 * @method \string[] getOrderTopicList()
	 * @method \string[] fillOrderTopic()
	 * @method \string[] getXmlIdList()
	 * @method \string[] fillXmlId()
	 * @method \string[] getId1cList()
	 * @method \string[] fillId1c()
	 * @method \string[] getVersion1cList()
	 * @method \string[] fillVersion1c()
	 * @method \int[] getVersionList()
	 * @method \int[] fillVersion()
	 * @method \boolean[] getExternalOrderList()
	 * @method \boolean[] fillExternalOrder()
	 * @method \int[] getStoreIdList()
	 * @method \int[] fillStoreId()
	 * @method \boolean[] getCanceledList()
	 * @method \boolean[] fillCanceled()
	 * @method \int[] getEmpCanceledIdList()
	 * @method \int[] fillEmpCanceledId()
	 * @method \Bitrix\Main\EO_User[] getEmpCanceledByList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getEmpCanceledByCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillEmpCanceledBy()
	 * @method \Bitrix\Main\Type\DateTime[] getDateCanceledList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateCanceled()
	 * @method \string[] getDateCanceledShortList()
	 * @method \string[] fillDateCanceledShort()
	 * @method \string[] getReasonCanceledList()
	 * @method \string[] fillReasonCanceled()
	 * @method \string[] getBxUserIdList()
	 * @method \string[] fillBxUserId()
	 * @method \string[] getSearchContentList()
	 * @method \string[] fillSearchContent()
	 * @method \boolean[] getRunningList()
	 * @method \boolean[] fillRunning()
	 * @method \Bitrix\Sale\Internals\EO_OrderCoupons[] getOrderCouponsList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getOrderCouponsCollection()
	 * @method \Bitrix\Sale\Internals\EO_OrderCoupons_Collection fillOrderCoupons()
	 * @method \Bitrix\Sale\Internals\EO_OrderDiscountData[] getOrderDiscountDataList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getOrderDiscountDataCollection()
	 * @method \Bitrix\Sale\Internals\EO_OrderDiscountData_Collection fillOrderDiscountData()
	 * @method \Bitrix\Sale\Internals\EO_OrderRules[] getOrderDiscountRulesList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getOrderDiscountRulesCollection()
	 * @method \Bitrix\Sale\Internals\EO_OrderRules_Collection fillOrderDiscountRules()
	 * @method \string[] getByRecommendationList()
	 * @method \string[] fillByRecommendation()
	 * @method \Bitrix\Sale\TradingPlatform\EO_Order[] getTradingPlatformList()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection getTradingPlatformCollection()
	 * @method \Bitrix\Sale\TradingPlatform\EO_Order_Collection fillTradingPlatform()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_Order $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_Order $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Order getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Order[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_Order $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_Order_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_Order current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Order_Collection merge(?EO_Order_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Order_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\OrderTable */
		static public $dataClass = '\Bitrix\Sale\Internals\OrderTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_Order_Query query()
	 * @method static EO_Order_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Order_Result getById($id)
	 * @method static EO_Order_Result getList(array $parameters = [])
	 * @method static EO_Order_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_Order createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_Order_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_Order wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_Order_Collection wakeUpCollection($rows)
	 */
	class OrderTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Order_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_Order fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Order_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Order fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection fetchCollection()
	 */
	class EO_Order_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Order createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_Order wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection wakeUpCollection($rows)
	 */
	class EO_Order_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\FuserTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Fuser
	 * @see \Bitrix\Sale\Internals\FuserTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Bitrix\Sale\Internals\EO_Fuser setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \Bitrix\Main\Type\DateTime getDateInsert()
	 * @method \Bitrix\Sale\Internals\EO_Fuser setDateInsert(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateInsert)
	 * @method bool hasDateInsert()
	 * @method bool isDateInsertFilled()
	 * @method bool isDateInsertChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateInsert()
	 * @method \Bitrix\Main\Type\DateTime requireDateInsert()
	 * @method \Bitrix\Sale\Internals\EO_Fuser resetDateInsert()
	 * @method \Bitrix\Sale\Internals\EO_Fuser unsetDateInsert()
	 * @method \Bitrix\Main\Type\DateTime fillDateInsert()
	 * @method \Bitrix\Main\Type\DateTime getDateIns()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateIns()
	 * @method \Bitrix\Main\Type\DateTime requireDateIns()
	 * @method bool hasDateIns()
	 * @method bool isDateInsFilled()
	 * @method \Bitrix\Sale\Internals\EO_Fuser unsetDateIns()
	 * @method \Bitrix\Main\Type\DateTime fillDateIns()
	 * @method \Bitrix\Main\Type\DateTime getDateUpdate()
	 * @method \Bitrix\Sale\Internals\EO_Fuser setDateUpdate(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateUpdate)
	 * @method bool hasDateUpdate()
	 * @method bool isDateUpdateFilled()
	 * @method bool isDateUpdateChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateUpdate()
	 * @method \Bitrix\Main\Type\DateTime requireDateUpdate()
	 * @method \Bitrix\Sale\Internals\EO_Fuser resetDateUpdate()
	 * @method \Bitrix\Sale\Internals\EO_Fuser unsetDateUpdate()
	 * @method \Bitrix\Main\Type\DateTime fillDateUpdate()
	 * @method \Bitrix\Main\Type\DateTime getDateUpd()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateUpd()
	 * @method \Bitrix\Main\Type\DateTime requireDateUpd()
	 * @method bool hasDateUpd()
	 * @method bool isDateUpdFilled()
	 * @method \Bitrix\Sale\Internals\EO_Fuser unsetDateUpd()
	 * @method \Bitrix\Main\Type\DateTime fillDateUpd()
	 * @method \int getUserId()
	 * @method \Bitrix\Sale\Internals\EO_Fuser setUserId(\int|\Bitrix\Main\DB\SqlExpression $userId)
	 * @method bool hasUserId()
	 * @method bool isUserIdFilled()
	 * @method bool isUserIdChanged()
	 * @method \int remindActualUserId()
	 * @method \int requireUserId()
	 * @method \Bitrix\Sale\Internals\EO_Fuser resetUserId()
	 * @method \Bitrix\Sale\Internals\EO_Fuser unsetUserId()
	 * @method \int fillUserId()
	 * @method \Bitrix\Main\EO_User getUser()
	 * @method \Bitrix\Main\EO_User remindActualUser()
	 * @method \Bitrix\Main\EO_User requireUser()
	 * @method \Bitrix\Sale\Internals\EO_Fuser setUser(\Bitrix\Main\EO_User $object)
	 * @method \Bitrix\Sale\Internals\EO_Fuser resetUser()
	 * @method \Bitrix\Sale\Internals\EO_Fuser unsetUser()
	 * @method bool hasUser()
	 * @method bool isUserFilled()
	 * @method bool isUserChanged()
	 * @method \Bitrix\Main\EO_User fillUser()
	 * @method \string getCode()
	 * @method \Bitrix\Sale\Internals\EO_Fuser setCode(\string|\Bitrix\Main\DB\SqlExpression $code)
	 * @method bool hasCode()
	 * @method bool isCodeFilled()
	 * @method bool isCodeChanged()
	 * @method \string remindActualCode()
	 * @method \string requireCode()
	 * @method \Bitrix\Sale\Internals\EO_Fuser resetCode()
	 * @method \Bitrix\Sale\Internals\EO_Fuser unsetCode()
	 * @method \string fillCode()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Fuser set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_Fuser reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Fuser unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_Fuser wakeUp($data)
	 */
	class EO_Fuser {
		/* @var \Bitrix\Sale\Internals\FuserTable */
		static public $dataClass = '\Bitrix\Sale\Internals\FuserTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Fuser_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \Bitrix\Main\Type\DateTime[] getDateInsertList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateInsert()
	 * @method \Bitrix\Main\Type\DateTime[] getDateInsList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateIns()
	 * @method \Bitrix\Main\Type\DateTime[] getDateUpdateList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateUpdate()
	 * @method \Bitrix\Main\Type\DateTime[] getDateUpdList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateUpd()
	 * @method \int[] getUserIdList()
	 * @method \int[] fillUserId()
	 * @method \Bitrix\Main\EO_User[] getUserList()
	 * @method \Bitrix\Sale\Internals\EO_Fuser_Collection getUserCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillUser()
	 * @method \string[] getCodeList()
	 * @method \string[] fillCode()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_Fuser $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_Fuser $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Fuser getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Fuser[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_Fuser $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_Fuser_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_Fuser current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Fuser_Collection merge(?EO_Fuser_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Fuser_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\FuserTable */
		static public $dataClass = '\Bitrix\Sale\Internals\FuserTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_Fuser_Query query()
	 * @method static EO_Fuser_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Fuser_Result getById($id)
	 * @method static EO_Fuser_Result getList(array $parameters = [])
	 * @method static EO_Fuser_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_Fuser createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_Fuser_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_Fuser wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_Fuser_Collection wakeUpCollection($rows)
	 */
	class FuserTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Fuser_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_Fuser fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Fuser_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Fuser_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Fuser fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Fuser_Collection fetchCollection()
	 */
	class EO_Fuser_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Fuser createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_Fuser_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_Fuser wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_Fuser_Collection wakeUpCollection($rows)
	 */
	class EO_Fuser_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\Product2ProductTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Product2Product
	 * @see \Bitrix\Sale\Internals\Product2ProductTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \int getProductId()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product setProductId(\int|\Bitrix\Main\DB\SqlExpression $productId)
	 * @method bool hasProductId()
	 * @method bool isProductIdFilled()
	 * @method bool isProductIdChanged()
	 * @method \int remindActualProductId()
	 * @method \int requireProductId()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product resetProductId()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product unsetProductId()
	 * @method \int fillProductId()
	 * @method \int getParentProductId()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product setParentProductId(\int|\Bitrix\Main\DB\SqlExpression $parentProductId)
	 * @method bool hasParentProductId()
	 * @method bool isParentProductIdFilled()
	 * @method bool isParentProductIdChanged()
	 * @method \int remindActualParentProductId()
	 * @method \int requireParentProductId()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product resetParentProductId()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product unsetParentProductId()
	 * @method \int fillParentProductId()
	 * @method \int getCnt()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product setCnt(\int|\Bitrix\Main\DB\SqlExpression $cnt)
	 * @method bool hasCnt()
	 * @method bool isCntFilled()
	 * @method bool isCntChanged()
	 * @method \int remindActualCnt()
	 * @method \int requireCnt()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product resetCnt()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product unsetCnt()
	 * @method \int fillCnt()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Product2Product set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_Product2Product reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Product2Product unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_Product2Product wakeUp($data)
	 */
	class EO_Product2Product {
		/* @var \Bitrix\Sale\Internals\Product2ProductTable */
		static public $dataClass = '\Bitrix\Sale\Internals\Product2ProductTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Product2Product_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \int[] getProductIdList()
	 * @method \int[] fillProductId()
	 * @method \int[] getParentProductIdList()
	 * @method \int[] fillParentProductId()
	 * @method \int[] getCntList()
	 * @method \int[] fillCnt()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_Product2Product $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_Product2Product $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Product2Product getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Product2Product[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_Product2Product $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_Product2Product_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_Product2Product current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Product2Product_Collection merge(?EO_Product2Product_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Product2Product_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\Product2ProductTable */
		static public $dataClass = '\Bitrix\Sale\Internals\Product2ProductTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_Product2Product_Query query()
	 * @method static EO_Product2Product_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Product2Product_Result getById($id)
	 * @method static EO_Product2Product_Result getList(array $parameters = [])
	 * @method static EO_Product2Product_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_Product2Product createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_Product2Product_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_Product2Product wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_Product2Product_Collection wakeUpCollection($rows)
	 */
	class Product2ProductTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Product2Product_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Product2Product_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Product2Product fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product_Collection fetchCollection()
	 */
	class EO_Product2Product_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Product2Product createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_Product2Product_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_Product2Product wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_Product2Product_Collection wakeUpCollection($rows)
	 */
	class EO_Product2Product_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\StoreProductTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_StoreProduct
	 * @see \Bitrix\Sale\Internals\StoreProductTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \int getProductId()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct setProductId(\int|\Bitrix\Main\DB\SqlExpression $productId)
	 * @method bool hasProductId()
	 * @method bool isProductIdFilled()
	 * @method bool isProductIdChanged()
	 * @method \int remindActualProductId()
	 * @method \int requireProductId()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct resetProductId()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct unsetProductId()
	 * @method \int fillProductId()
	 * @method \Bitrix\Sale\Internals\EO_Product getSaleProduct()
	 * @method \Bitrix\Sale\Internals\EO_Product remindActualSaleProduct()
	 * @method \Bitrix\Sale\Internals\EO_Product requireSaleProduct()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct setSaleProduct(\Bitrix\Sale\Internals\EO_Product $object)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct resetSaleProduct()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct unsetSaleProduct()
	 * @method bool hasSaleProduct()
	 * @method bool isSaleProductFilled()
	 * @method bool isSaleProductChanged()
	 * @method \Bitrix\Sale\Internals\EO_Product fillSaleProduct()
	 * @method \float getAmount()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct setAmount(\float|\Bitrix\Main\DB\SqlExpression $amount)
	 * @method bool hasAmount()
	 * @method bool isAmountFilled()
	 * @method bool isAmountChanged()
	 * @method \float remindActualAmount()
	 * @method \float requireAmount()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct resetAmount()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct unsetAmount()
	 * @method \float fillAmount()
	 * @method \int getStoreId()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct setStoreId(\int|\Bitrix\Main\DB\SqlExpression $storeId)
	 * @method bool hasStoreId()
	 * @method bool isStoreIdFilled()
	 * @method bool isStoreIdChanged()
	 * @method \int remindActualStoreId()
	 * @method \int requireStoreId()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct resetStoreId()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct unsetStoreId()
	 * @method \int fillStoreId()
	 * @method \Bitrix\Catalog\EO_Store getStore()
	 * @method \Bitrix\Catalog\EO_Store remindActualStore()
	 * @method \Bitrix\Catalog\EO_Store requireStore()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct setStore(\Bitrix\Catalog\EO_Store $object)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct resetStore()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct unsetStore()
	 * @method bool hasStore()
	 * @method bool isStoreFilled()
	 * @method bool isStoreChanged()
	 * @method \Bitrix\Catalog\EO_Store fillStore()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_StoreProduct wakeUp($data)
	 */
	class EO_StoreProduct {
		/* @var \Bitrix\Sale\Internals\StoreProductTable */
		static public $dataClass = '\Bitrix\Sale\Internals\StoreProductTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_StoreProduct_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \int[] getProductIdList()
	 * @method \int[] fillProductId()
	 * @method \Bitrix\Sale\Internals\EO_Product[] getSaleProductList()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct_Collection getSaleProductCollection()
	 * @method \Bitrix\Sale\Internals\EO_Product_Collection fillSaleProduct()
	 * @method \float[] getAmountList()
	 * @method \float[] fillAmount()
	 * @method \int[] getStoreIdList()
	 * @method \int[] fillStoreId()
	 * @method \Bitrix\Catalog\EO_Store[] getStoreList()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct_Collection getStoreCollection()
	 * @method \Bitrix\Catalog\EO_Store_Collection fillStore()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_StoreProduct $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_StoreProduct $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_StoreProduct $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_StoreProduct_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_StoreProduct_Collection merge(?EO_StoreProduct_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_StoreProduct_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\StoreProductTable */
		static public $dataClass = '\Bitrix\Sale\Internals\StoreProductTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_StoreProduct_Query query()
	 * @method static EO_StoreProduct_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_StoreProduct_Result getById($id)
	 * @method static EO_StoreProduct_Result getList(array $parameters = [])
	 * @method static EO_StoreProduct_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_StoreProduct createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_StoreProduct_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_StoreProduct wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_StoreProduct_Collection wakeUpCollection($rows)
	 */
	class StoreProductTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_StoreProduct_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_StoreProduct_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct_Collection fetchCollection()
	 */
	class EO_StoreProduct_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_StoreProduct_Collection wakeUpCollection($rows)
	 */
	class EO_StoreProduct_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\PersonTypeTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_PersonType
	 * @see \Bitrix\Sale\Internals\PersonTypeTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \string getLid()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setLid(\string|\Bitrix\Main\DB\SqlExpression $lid)
	 * @method bool hasLid()
	 * @method bool isLidFilled()
	 * @method bool isLidChanged()
	 * @method \string remindActualLid()
	 * @method \string requireLid()
	 * @method \Bitrix\Sale\Internals\EO_PersonType resetLid()
	 * @method \Bitrix\Sale\Internals\EO_PersonType unsetLid()
	 * @method \string fillLid()
	 * @method \Bitrix\Sale\Internals\EO_PersonTypeSite getPersonTypeSite()
	 * @method \Bitrix\Sale\Internals\EO_PersonTypeSite remindActualPersonTypeSite()
	 * @method \Bitrix\Sale\Internals\EO_PersonTypeSite requirePersonTypeSite()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setPersonTypeSite(\Bitrix\Sale\Internals\EO_PersonTypeSite $object)
	 * @method \Bitrix\Sale\Internals\EO_PersonType resetPersonTypeSite()
	 * @method \Bitrix\Sale\Internals\EO_PersonType unsetPersonTypeSite()
	 * @method bool hasPersonTypeSite()
	 * @method bool isPersonTypeSiteFilled()
	 * @method bool isPersonTypeSiteChanged()
	 * @method \Bitrix\Sale\Internals\EO_PersonTypeSite fillPersonTypeSite()
	 * @method \string getName()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setName(\string|\Bitrix\Main\DB\SqlExpression $name)
	 * @method bool hasName()
	 * @method bool isNameFilled()
	 * @method bool isNameChanged()
	 * @method \string remindActualName()
	 * @method \string requireName()
	 * @method \Bitrix\Sale\Internals\EO_PersonType resetName()
	 * @method \Bitrix\Sale\Internals\EO_PersonType unsetName()
	 * @method \string fillName()
	 * @method \string getCode()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setCode(\string|\Bitrix\Main\DB\SqlExpression $code)
	 * @method bool hasCode()
	 * @method bool isCodeFilled()
	 * @method bool isCodeChanged()
	 * @method \string remindActualCode()
	 * @method \string requireCode()
	 * @method \Bitrix\Sale\Internals\EO_PersonType resetCode()
	 * @method \Bitrix\Sale\Internals\EO_PersonType unsetCode()
	 * @method \string fillCode()
	 * @method \int getSort()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setSort(\int|\Bitrix\Main\DB\SqlExpression $sort)
	 * @method bool hasSort()
	 * @method bool isSortFilled()
	 * @method bool isSortChanged()
	 * @method \int remindActualSort()
	 * @method \int requireSort()
	 * @method \Bitrix\Sale\Internals\EO_PersonType resetSort()
	 * @method \Bitrix\Sale\Internals\EO_PersonType unsetSort()
	 * @method \int fillSort()
	 * @method \boolean getActive()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setActive(\boolean|\Bitrix\Main\DB\SqlExpression $active)
	 * @method bool hasActive()
	 * @method bool isActiveFilled()
	 * @method bool isActiveChanged()
	 * @method \boolean remindActualActive()
	 * @method \boolean requireActive()
	 * @method \Bitrix\Sale\Internals\EO_PersonType resetActive()
	 * @method \Bitrix\Sale\Internals\EO_PersonType unsetActive()
	 * @method \boolean fillActive()
	 * @method \string getXmlId()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setXmlId(\string|\Bitrix\Main\DB\SqlExpression $xmlId)
	 * @method bool hasXmlId()
	 * @method bool isXmlIdFilled()
	 * @method bool isXmlIdChanged()
	 * @method \string remindActualXmlId()
	 * @method \string requireXmlId()
	 * @method \Bitrix\Sale\Internals\EO_PersonType resetXmlId()
	 * @method \Bitrix\Sale\Internals\EO_PersonType unsetXmlId()
	 * @method \string fillXmlId()
	 * @method \string getEntityRegistryType()
	 * @method \Bitrix\Sale\Internals\EO_PersonType setEntityRegistryType(\string|\Bitrix\Main\DB\SqlExpression $entityRegistryType)
	 * @method bool hasEntityRegistryType()
	 * @method bool isEntityRegistryTypeFilled()
	 * @method bool isEntityRegistryTypeChanged()
	 * @method \string remindActualEntityRegistryType()
	 * @method \string requireEntityRegistryType()
	 * @method \Bitrix\Sale\Internals\EO_PersonType resetEntityRegistryType()
	 * @method \Bitrix\Sale\Internals\EO_PersonType unsetEntityRegistryType()
	 * @method \string fillEntityRegistryType()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_PersonType set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_PersonType reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_PersonType unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_PersonType wakeUp($data)
	 */
	class EO_PersonType {
		/* @var \Bitrix\Sale\Internals\PersonTypeTable */
		static public $dataClass = '\Bitrix\Sale\Internals\PersonTypeTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_PersonType_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \string[] getLidList()
	 * @method \string[] fillLid()
	 * @method \Bitrix\Sale\Internals\EO_PersonTypeSite[] getPersonTypeSiteList()
	 * @method \Bitrix\Sale\Internals\EO_PersonType_Collection getPersonTypeSiteCollection()
	 * @method \Bitrix\Sale\Internals\EO_PersonTypeSite_Collection fillPersonTypeSite()
	 * @method \string[] getNameList()
	 * @method \string[] fillName()
	 * @method \string[] getCodeList()
	 * @method \string[] fillCode()
	 * @method \int[] getSortList()
	 * @method \int[] fillSort()
	 * @method \boolean[] getActiveList()
	 * @method \boolean[] fillActive()
	 * @method \string[] getXmlIdList()
	 * @method \string[] fillXmlId()
	 * @method \string[] getEntityRegistryTypeList()
	 * @method \string[] fillEntityRegistryType()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_PersonType $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_PersonType $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_PersonType getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_PersonType[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_PersonType $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_PersonType_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_PersonType current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_PersonType_Collection merge(?EO_PersonType_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_PersonType_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\PersonTypeTable */
		static public $dataClass = '\Bitrix\Sale\Internals\PersonTypeTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_PersonType_Query query()
	 * @method static EO_PersonType_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_PersonType_Result getById($id)
	 * @method static EO_PersonType_Result getList(array $parameters = [])
	 * @method static EO_PersonType_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_PersonType createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_PersonType_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_PersonType wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_PersonType_Collection wakeUpCollection($rows)
	 */
	class PersonTypeTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_PersonType_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_PersonType fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_PersonType_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_PersonType_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_PersonType fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_PersonType_Collection fetchCollection()
	 */
	class EO_PersonType_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_PersonType createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_PersonType_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_PersonType wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_PersonType_Collection wakeUpCollection($rows)
	 */
	class EO_PersonType_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\ProductTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Product
	 * @see \Bitrix\Sale\Internals\ProductTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Bitrix\Sale\Internals\EO_Product setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \int getTimestampX()
	 * @method \Bitrix\Sale\Internals\EO_Product setTimestampX(\int|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \int remindActualTimestampX()
	 * @method \int requireTimestampX()
	 * @method \Bitrix\Sale\Internals\EO_Product resetTimestampX()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetTimestampX()
	 * @method \int fillTimestampX()
	 * @method \Bitrix\Main\Type\DateTime getDateUpdated()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateUpdated()
	 * @method \Bitrix\Main\Type\DateTime requireDateUpdated()
	 * @method bool hasDateUpdated()
	 * @method bool isDateUpdatedFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetDateUpdated()
	 * @method \Bitrix\Main\Type\DateTime fillDateUpdated()
	 * @method \float getQuantity()
	 * @method \Bitrix\Sale\Internals\EO_Product setQuantity(\float|\Bitrix\Main\DB\SqlExpression $quantity)
	 * @method bool hasQuantity()
	 * @method bool isQuantityFilled()
	 * @method bool isQuantityChanged()
	 * @method \float remindActualQuantity()
	 * @method \float requireQuantity()
	 * @method \Bitrix\Sale\Internals\EO_Product resetQuantity()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetQuantity()
	 * @method \float fillQuantity()
	 * @method \int getMeasure()
	 * @method \Bitrix\Sale\Internals\EO_Product setMeasure(\int|\Bitrix\Main\DB\SqlExpression $measure)
	 * @method bool hasMeasure()
	 * @method bool isMeasureFilled()
	 * @method bool isMeasureChanged()
	 * @method \int remindActualMeasure()
	 * @method \int requireMeasure()
	 * @method \Bitrix\Sale\Internals\EO_Product resetMeasure()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetMeasure()
	 * @method \int fillMeasure()
	 * @method \float getPurchasingPrice()
	 * @method \Bitrix\Sale\Internals\EO_Product setPurchasingPrice(\float|\Bitrix\Main\DB\SqlExpression $purchasingPrice)
	 * @method bool hasPurchasingPrice()
	 * @method bool isPurchasingPriceFilled()
	 * @method bool isPurchasingPriceChanged()
	 * @method \float remindActualPurchasingPrice()
	 * @method \float requirePurchasingPrice()
	 * @method \Bitrix\Sale\Internals\EO_Product resetPurchasingPrice()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetPurchasingPrice()
	 * @method \float fillPurchasingPrice()
	 * @method \string getPurchasingCurrency()
	 * @method \Bitrix\Sale\Internals\EO_Product setPurchasingCurrency(\string|\Bitrix\Main\DB\SqlExpression $purchasingCurrency)
	 * @method bool hasPurchasingCurrency()
	 * @method bool isPurchasingCurrencyFilled()
	 * @method bool isPurchasingCurrencyChanged()
	 * @method \string remindActualPurchasingCurrency()
	 * @method \string requirePurchasingCurrency()
	 * @method \Bitrix\Sale\Internals\EO_Product resetPurchasingCurrency()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetPurchasingCurrency()
	 * @method \string fillPurchasingCurrency()
	 * @method \Bitrix\Iblock\EO_Element getIblock()
	 * @method \Bitrix\Iblock\EO_Element remindActualIblock()
	 * @method \Bitrix\Iblock\EO_Element requireIblock()
	 * @method \Bitrix\Sale\Internals\EO_Product setIblock(\Bitrix\Iblock\EO_Element $object)
	 * @method \Bitrix\Sale\Internals\EO_Product resetIblock()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetIblock()
	 * @method bool hasIblock()
	 * @method bool isIblockFilled()
	 * @method bool isIblockChanged()
	 * @method \Bitrix\Iblock\EO_Element fillIblock()
	 * @method \string getName()
	 * @method \string remindActualName()
	 * @method \string requireName()
	 * @method bool hasName()
	 * @method bool isNameFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetName()
	 * @method \string fillName()
	 * @method \string getNameWithIdent()
	 * @method \string remindActualNameWithIdent()
	 * @method \string requireNameWithIdent()
	 * @method bool hasNameWithIdent()
	 * @method bool isNameWithIdentFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetNameWithIdent()
	 * @method \string fillNameWithIdent()
	 * @method \boolean getActive()
	 * @method \boolean remindActualActive()
	 * @method \boolean requireActive()
	 * @method bool hasActive()
	 * @method bool isActiveFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetActive()
	 * @method \boolean fillActive()
	 * @method \float getWeight()
	 * @method \Bitrix\Sale\Internals\EO_Product setWeight(\float|\Bitrix\Main\DB\SqlExpression $weight)
	 * @method bool hasWeight()
	 * @method bool isWeightFilled()
	 * @method bool isWeightChanged()
	 * @method \float remindActualWeight()
	 * @method \float requireWeight()
	 * @method \Bitrix\Sale\Internals\EO_Product resetWeight()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetWeight()
	 * @method \float fillWeight()
	 * @method \float getWeightInSiteUnits()
	 * @method \float remindActualWeightInSiteUnits()
	 * @method \float requireWeightInSiteUnits()
	 * @method bool hasWeightInSiteUnits()
	 * @method bool isWeightInSiteUnitsFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetWeightInSiteUnits()
	 * @method \float fillWeightInSiteUnits()
	 * @method \float getPrice()
	 * @method \float remindActualPrice()
	 * @method \float requirePrice()
	 * @method bool hasPrice()
	 * @method bool isPriceFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetPrice()
	 * @method \float fillPrice()
	 * @method \string getCurrency()
	 * @method \string remindActualCurrency()
	 * @method \string requireCurrency()
	 * @method bool hasCurrency()
	 * @method bool isCurrencyFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetCurrency()
	 * @method \string fillCurrency()
	 * @method \float getSummaryPrice()
	 * @method \float remindActualSummaryPrice()
	 * @method \float requireSummaryPrice()
	 * @method bool hasSummaryPrice()
	 * @method bool isSummaryPriceFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetSummaryPrice()
	 * @method \float fillSummaryPrice()
	 * @method \float getCurrentCurrencyRate()
	 * @method \float remindActualCurrentCurrencyRate()
	 * @method \float requireCurrentCurrencyRate()
	 * @method bool hasCurrentCurrencyRate()
	 * @method bool isCurrentCurrencyRateFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetCurrentCurrencyRate()
	 * @method \float fillCurrentCurrencyRate()
	 * @method \float getCurrentCurrencyRateCnt()
	 * @method \float remindActualCurrentCurrencyRateCnt()
	 * @method \float requireCurrentCurrencyRateCnt()
	 * @method bool hasCurrentCurrencyRateCnt()
	 * @method bool isCurrentCurrencyRateCntFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetCurrentCurrencyRateCnt()
	 * @method \float fillCurrentCurrencyRateCnt()
	 * @method \float getCurrentSiteCurrencyRate()
	 * @method \float remindActualCurrentSiteCurrencyRate()
	 * @method \float requireCurrentSiteCurrencyRate()
	 * @method bool hasCurrentSiteCurrencyRate()
	 * @method bool isCurrentSiteCurrencyRateFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetCurrentSiteCurrencyRate()
	 * @method \float fillCurrentSiteCurrencyRate()
	 * @method \float getCurrentSiteCurrencyRateCnt()
	 * @method \float remindActualCurrentSiteCurrencyRateCnt()
	 * @method \float requireCurrentSiteCurrencyRateCnt()
	 * @method bool hasCurrentSiteCurrencyRateCnt()
	 * @method bool isCurrentSiteCurrencyRateCntFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetCurrentSiteCurrencyRateCnt()
	 * @method \float fillCurrentSiteCurrencyRateCnt()
	 * @method \float getPurchasingCurrencyRate()
	 * @method \float remindActualPurchasingCurrencyRate()
	 * @method \float requirePurchasingCurrencyRate()
	 * @method bool hasPurchasingCurrencyRate()
	 * @method bool isPurchasingCurrencyRateFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetPurchasingCurrencyRate()
	 * @method \float fillPurchasingCurrencyRate()
	 * @method \float getPurchasingCurrencyRateCnt()
	 * @method \float remindActualPurchasingCurrencyRateCnt()
	 * @method \float requirePurchasingCurrencyRateCnt()
	 * @method bool hasPurchasingCurrencyRateCnt()
	 * @method bool isPurchasingCurrencyRateCntFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetPurchasingCurrencyRateCnt()
	 * @method \float fillPurchasingCurrencyRateCnt()
	 * @method \float getPriceInSiteCurrency()
	 * @method \float remindActualPriceInSiteCurrency()
	 * @method \float requirePriceInSiteCurrency()
	 * @method bool hasPriceInSiteCurrency()
	 * @method bool isPriceInSiteCurrencyFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetPriceInSiteCurrency()
	 * @method \float fillPriceInSiteCurrency()
	 * @method \float getPurchasingPriceInSiteCurrency()
	 * @method \float remindActualPurchasingPriceInSiteCurrency()
	 * @method \float requirePurchasingPriceInSiteCurrency()
	 * @method bool hasPurchasingPriceInSiteCurrency()
	 * @method bool isPurchasingPriceInSiteCurrencyFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetPurchasingPriceInSiteCurrency()
	 * @method \float fillPurchasingPriceInSiteCurrency()
	 * @method \float getSummaryPriceInSiteCurrency()
	 * @method \float remindActualSummaryPriceInSiteCurrency()
	 * @method \float requireSummaryPriceInSiteCurrency()
	 * @method bool hasSummaryPriceInSiteCurrency()
	 * @method bool isSummaryPriceInSiteCurrencyFilled()
	 * @method \Bitrix\Sale\Internals\EO_Product unsetSummaryPriceInSiteCurrency()
	 * @method \float fillSummaryPriceInSiteCurrency()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Product set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_Product reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Product unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_Product wakeUp($data)
	 */
	class EO_Product {
		/* @var \Bitrix\Sale\Internals\ProductTable */
		static public $dataClass = '\Bitrix\Sale\Internals\ProductTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Product_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \int[] getTimestampXList()
	 * @method \int[] fillTimestampX()
	 * @method \Bitrix\Main\Type\DateTime[] getDateUpdatedList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateUpdated()
	 * @method \float[] getQuantityList()
	 * @method \float[] fillQuantity()
	 * @method \int[] getMeasureList()
	 * @method \int[] fillMeasure()
	 * @method \float[] getPurchasingPriceList()
	 * @method \float[] fillPurchasingPrice()
	 * @method \string[] getPurchasingCurrencyList()
	 * @method \string[] fillPurchasingCurrency()
	 * @method \Bitrix\Iblock\EO_Element[] getIblockList()
	 * @method \Bitrix\Sale\Internals\EO_Product_Collection getIblockCollection()
	 * @method \Bitrix\Iblock\EO_Element_Collection fillIblock()
	 * @method \string[] getNameList()
	 * @method \string[] fillName()
	 * @method \string[] getNameWithIdentList()
	 * @method \string[] fillNameWithIdent()
	 * @method \boolean[] getActiveList()
	 * @method \boolean[] fillActive()
	 * @method \float[] getWeightList()
	 * @method \float[] fillWeight()
	 * @method \float[] getWeightInSiteUnitsList()
	 * @method \float[] fillWeightInSiteUnits()
	 * @method \float[] getPriceList()
	 * @method \float[] fillPrice()
	 * @method \string[] getCurrencyList()
	 * @method \string[] fillCurrency()
	 * @method \float[] getSummaryPriceList()
	 * @method \float[] fillSummaryPrice()
	 * @method \float[] getCurrentCurrencyRateList()
	 * @method \float[] fillCurrentCurrencyRate()
	 * @method \float[] getCurrentCurrencyRateCntList()
	 * @method \float[] fillCurrentCurrencyRateCnt()
	 * @method \float[] getCurrentSiteCurrencyRateList()
	 * @method \float[] fillCurrentSiteCurrencyRate()
	 * @method \float[] getCurrentSiteCurrencyRateCntList()
	 * @method \float[] fillCurrentSiteCurrencyRateCnt()
	 * @method \float[] getPurchasingCurrencyRateList()
	 * @method \float[] fillPurchasingCurrencyRate()
	 * @method \float[] getPurchasingCurrencyRateCntList()
	 * @method \float[] fillPurchasingCurrencyRateCnt()
	 * @method \float[] getPriceInSiteCurrencyList()
	 * @method \float[] fillPriceInSiteCurrency()
	 * @method \float[] getPurchasingPriceInSiteCurrencyList()
	 * @method \float[] fillPurchasingPriceInSiteCurrency()
	 * @method \float[] getSummaryPriceInSiteCurrencyList()
	 * @method \float[] fillSummaryPriceInSiteCurrency()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_Product $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_Product $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Product getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Product[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_Product $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_Product_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_Product current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Product_Collection merge(?EO_Product_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Product_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\ProductTable */
		static public $dataClass = '\Bitrix\Sale\Internals\ProductTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_Product_Query query()
	 * @method static EO_Product_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Product_Result getById($id)
	 * @method static EO_Product_Result getList(array $parameters = [])
	 * @method static EO_Product_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_Product createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_Product_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_Product wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_Product_Collection wakeUpCollection($rows)
	 */
	class ProductTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Product_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_Product fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Product_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Product_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Product fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Product_Collection fetchCollection()
	 */
	class EO_Product_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Product createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_Product_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_Product wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_Product_Collection wakeUpCollection($rows)
	 */
	class EO_Product_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\SectionTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Section
	 * @see \Bitrix\Sale\Internals\SectionTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Bitrix\Sale\Internals\EO_Section setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \string getName()
	 * @method \Bitrix\Sale\Internals\EO_Section setName(\string|\Bitrix\Main\DB\SqlExpression $name)
	 * @method bool hasName()
	 * @method bool isNameFilled()
	 * @method bool isNameChanged()
	 * @method \string remindActualName()
	 * @method \string requireName()
	 * @method \Bitrix\Sale\Internals\EO_Section resetName()
	 * @method \Bitrix\Sale\Internals\EO_Section unsetName()
	 * @method \string fillName()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Section set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_Section reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_Section unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_Section wakeUp($data)
	 */
	class EO_Section {
		/* @var \Bitrix\Sale\Internals\SectionTable */
		static public $dataClass = '\Bitrix\Sale\Internals\SectionTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_Section_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \string[] getNameList()
	 * @method \string[] fillName()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_Section $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_Section $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Section getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_Section[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_Section $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_Section_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_Section current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Section_Collection merge(?EO_Section_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Section_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\SectionTable */
		static public $dataClass = '\Bitrix\Sale\Internals\SectionTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_Section_Query query()
	 * @method static EO_Section_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Section_Result getById($id)
	 * @method static EO_Section_Result getList(array $parameters = [])
	 * @method static EO_Section_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_Section createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_Section_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_Section wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_Section_Collection wakeUpCollection($rows)
	 */
	class SectionTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Section_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_Section fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Section_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Section_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Section fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_Section_Collection fetchCollection()
	 */
	class EO_Section_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_Section createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_Section_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_Section wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_Section_Collection wakeUpCollection($rows)
	 */
	class EO_Section_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\OrderProcessingTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_OrderProcessing
	 * @see \Bitrix\Sale\Internals\OrderProcessingTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getOrderId()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing setOrderId(\int|\Bitrix\Main\DB\SqlExpression $orderId)
	 * @method bool hasOrderId()
	 * @method bool isOrderIdFilled()
	 * @method bool isOrderIdChanged()
	 * @method \boolean getProductsAdded()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing setProductsAdded(\boolean|\Bitrix\Main\DB\SqlExpression $productsAdded)
	 * @method bool hasProductsAdded()
	 * @method bool isProductsAddedFilled()
	 * @method bool isProductsAddedChanged()
	 * @method \boolean remindActualProductsAdded()
	 * @method \boolean requireProductsAdded()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing resetProductsAdded()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing unsetProductsAdded()
	 * @method \boolean fillProductsAdded()
	 * @method \boolean getProductsRemoved()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing setProductsRemoved(\boolean|\Bitrix\Main\DB\SqlExpression $productsRemoved)
	 * @method bool hasProductsRemoved()
	 * @method bool isProductsRemovedFilled()
	 * @method bool isProductsRemovedChanged()
	 * @method \boolean remindActualProductsRemoved()
	 * @method \boolean requireProductsRemoved()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing resetProductsRemoved()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing unsetProductsRemoved()
	 * @method \boolean fillProductsRemoved()
	 * @method \Bitrix\Sale\Internals\EO_Order getOrder()
	 * @method \Bitrix\Sale\Internals\EO_Order remindActualOrder()
	 * @method \Bitrix\Sale\Internals\EO_Order requireOrder()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing setOrder(\Bitrix\Sale\Internals\EO_Order $object)
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing resetOrder()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing unsetOrder()
	 * @method bool hasOrder()
	 * @method bool isOrderFilled()
	 * @method bool isOrderChanged()
	 * @method \Bitrix\Sale\Internals\EO_Order fillOrder()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_OrderProcessing wakeUp($data)
	 */
	class EO_OrderProcessing {
		/* @var \Bitrix\Sale\Internals\OrderProcessingTable */
		static public $dataClass = '\Bitrix\Sale\Internals\OrderProcessingTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_OrderProcessing_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getOrderIdList()
	 * @method \boolean[] getProductsAddedList()
	 * @method \boolean[] fillProductsAdded()
	 * @method \boolean[] getProductsRemovedList()
	 * @method \boolean[] fillProductsRemoved()
	 * @method \Bitrix\Sale\Internals\EO_Order[] getOrderList()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing_Collection getOrderCollection()
	 * @method \Bitrix\Sale\Internals\EO_Order_Collection fillOrder()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_OrderProcessing $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_OrderProcessing $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_OrderProcessing $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_OrderProcessing_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_OrderProcessing_Collection merge(?EO_OrderProcessing_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_OrderProcessing_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\OrderProcessingTable */
		static public $dataClass = '\Bitrix\Sale\Internals\OrderProcessingTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_OrderProcessing_Query query()
	 * @method static EO_OrderProcessing_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_OrderProcessing_Result getById($id)
	 * @method static EO_OrderProcessing_Result getList(array $parameters = [])
	 * @method static EO_OrderProcessing_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_OrderProcessing createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_OrderProcessing_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_OrderProcessing wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_OrderProcessing_Collection wakeUpCollection($rows)
	 */
	class OrderProcessingTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_OrderProcessing_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_OrderProcessing_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing_Collection fetchCollection()
	 */
	class EO_OrderProcessing_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_OrderProcessing_Collection wakeUpCollection($rows)
	 */
	class EO_OrderProcessing_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Bitrix\Sale\Internals\GoodsSectionTable */
namespace Bitrix\Sale\Internals {
	/**
	 * EO_GoodsSection
	 * @see \Bitrix\Sale\Internals\GoodsSectionTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getIblockElementId()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection setIblockElementId(\int|\Bitrix\Main\DB\SqlExpression $iblockElementId)
	 * @method bool hasIblockElementId()
	 * @method bool isIblockElementIdFilled()
	 * @method bool isIblockElementIdChanged()
	 * @method \Bitrix\Sale\Internals\EO_Product getProduct()
	 * @method \Bitrix\Sale\Internals\EO_Product remindActualProduct()
	 * @method \Bitrix\Sale\Internals\EO_Product requireProduct()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection setProduct(\Bitrix\Sale\Internals\EO_Product $object)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection resetProduct()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection unsetProduct()
	 * @method bool hasProduct()
	 * @method bool isProductFilled()
	 * @method bool isProductChanged()
	 * @method \Bitrix\Sale\Internals\EO_Product fillProduct()
	 * @method \int getIblockSectionId()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection setIblockSectionId(\int|\Bitrix\Main\DB\SqlExpression $iblockSectionId)
	 * @method bool hasIblockSectionId()
	 * @method bool isIblockSectionIdFilled()
	 * @method bool isIblockSectionIdChanged()
	 * @method \Bitrix\Sale\Internals\EO_Section getSect()
	 * @method \Bitrix\Sale\Internals\EO_Section remindActualSect()
	 * @method \Bitrix\Sale\Internals\EO_Section requireSect()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection setSect(\Bitrix\Sale\Internals\EO_Section $object)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection resetSect()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection unsetSect()
	 * @method bool hasSect()
	 * @method bool isSectFilled()
	 * @method bool isSectChanged()
	 * @method \Bitrix\Sale\Internals\EO_Section fillSect()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection set($fieldName, $value)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection reset($fieldName)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Bitrix\Sale\Internals\EO_GoodsSection wakeUp($data)
	 */
	class EO_GoodsSection {
		/* @var \Bitrix\Sale\Internals\GoodsSectionTable */
		static public $dataClass = '\Bitrix\Sale\Internals\GoodsSectionTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * EO_GoodsSection_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIblockElementIdList()
	 * @method \Bitrix\Sale\Internals\EO_Product[] getProductList()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection_Collection getProductCollection()
	 * @method \Bitrix\Sale\Internals\EO_Product_Collection fillProduct()
	 * @method \int[] getIblockSectionIdList()
	 * @method \Bitrix\Sale\Internals\EO_Section[] getSectList()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection_Collection getSectCollection()
	 * @method \Bitrix\Sale\Internals\EO_Section_Collection fillSect()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Bitrix\Sale\Internals\EO_GoodsSection $object)
	 * @method bool has(\Bitrix\Sale\Internals\EO_GoodsSection $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection getByPrimary($primary)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection[] getAll()
	 * @method bool remove(\Bitrix\Sale\Internals\EO_GoodsSection $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Bitrix\Sale\Internals\EO_GoodsSection_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_GoodsSection_Collection merge(?EO_GoodsSection_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_GoodsSection_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Bitrix\Sale\Internals\GoodsSectionTable */
		static public $dataClass = '\Bitrix\Sale\Internals\GoodsSectionTable';
	}
}
namespace Bitrix\Sale\Internals {
	/**
	 * @method static EO_GoodsSection_Query query()
	 * @method static EO_GoodsSection_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_GoodsSection_Result getById($id)
	 * @method static EO_GoodsSection_Result getList(array $parameters = [])
	 * @method static EO_GoodsSection_Entity getEntity()
	 * @method static \Bitrix\Sale\Internals\EO_GoodsSection createObject($setDefaultValues = true)
	 * @method static \Bitrix\Sale\Internals\EO_GoodsSection_Collection createCollection()
	 * @method static \Bitrix\Sale\Internals\EO_GoodsSection wakeUpObject($row)
	 * @method static \Bitrix\Sale\Internals\EO_GoodsSection_Collection wakeUpCollection($rows)
	 */
	class GoodsSectionTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_GoodsSection_Result exec()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_GoodsSection_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection fetchObject()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection_Collection fetchCollection()
	 */
	class EO_GoodsSection_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection createObject($setDefaultValues = true)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection_Collection createCollection()
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection wakeUpObject($row)
	 * @method \Bitrix\Sale\Internals\EO_GoodsSection_Collection wakeUpCollection($rows)
	 */
	class EO_GoodsSection_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Feed\Engine\Steps\Offer\Table */
namespace Avito\Export\Feed\Engine\Steps\Offer {
	/**
	 * EO_NNM_Object
	 * @see \Avito\Export\Feed\Engine\Steps\Offer\Table
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getFeedId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setFeedId(\int|\Bitrix\Main\DB\SqlExpression $feedId)
	 * @method bool hasFeedId()
	 * @method bool isFeedIdFilled()
	 * @method bool isFeedIdChanged()
	 * @method \int getElementId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setElementId(\int|\Bitrix\Main\DB\SqlExpression $elementId)
	 * @method bool hasElementId()
	 * @method bool isElementIdFilled()
	 * @method bool isElementIdChanged()
	 * @method \int getRegionId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setRegionId(\int|\Bitrix\Main\DB\SqlExpression $regionId)
	 * @method bool hasRegionId()
	 * @method bool isRegionIdFilled()
	 * @method bool isRegionIdChanged()
	 * @method \string getPrimary()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setPrimary(\string|\Bitrix\Main\DB\SqlExpression $primary)
	 * @method bool hasPrimary()
	 * @method bool isPrimaryFilled()
	 * @method bool isPrimaryChanged()
	 * @method \string remindActualPrimary()
	 * @method \string requirePrimary()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object resetPrimary()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object unsetPrimary()
	 * @method \string fillPrimary()
	 * @method \string getHash()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setHash(\string|\Bitrix\Main\DB\SqlExpression $hash)
	 * @method bool hasHash()
	 * @method bool isHashFilled()
	 * @method bool isHashChanged()
	 * @method \string remindActualHash()
	 * @method \string requireHash()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object resetHash()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object unsetHash()
	 * @method \string fillHash()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object resetTimestampX()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 * @method \int getIblockId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setIblockId(\int|\Bitrix\Main\DB\SqlExpression $iblockId)
	 * @method bool hasIblockId()
	 * @method bool isIblockIdFilled()
	 * @method bool isIblockIdChanged()
	 * @method \int remindActualIblockId()
	 * @method \int requireIblockId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object resetIblockId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object unsetIblockId()
	 * @method \int fillIblockId()
	 * @method \int getParentId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setParentId(\int|\Bitrix\Main\DB\SqlExpression $parentId)
	 * @method bool hasParentId()
	 * @method bool isParentIdFilled()
	 * @method bool isParentIdChanged()
	 * @method \int remindActualParentId()
	 * @method \int requireParentId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object resetParentId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object unsetParentId()
	 * @method \int fillParentId()
	 * @method \boolean getStatus()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object setStatus(\boolean|\Bitrix\Main\DB\SqlExpression $status)
	 * @method bool hasStatus()
	 * @method bool isStatusFilled()
	 * @method bool isStatusChanged()
	 * @method \boolean remindActualStatus()
	 * @method \boolean requireStatus()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object resetStatus()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object unsetStatus()
	 * @method \boolean fillStatus()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object set($fieldName, $value)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object reset($fieldName)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object wakeUp($data)
	 */
	class EO_NNM_Object {
		/* @var \Avito\Export\Feed\Engine\Steps\Offer\Table */
		static public $dataClass = '\Avito\Export\Feed\Engine\Steps\Offer\Table';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Feed\Engine\Steps\Offer {
	/**
	 * EO__Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getFeedIdList()
	 * @method \int[] getElementIdList()
	 * @method \int[] getRegionIdList()
	 * @method \string[] getPrimaryList()
	 * @method \string[] fillPrimary()
	 * @method \string[] getHashList()
	 * @method \string[] fillHash()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 * @method \int[] getIblockIdList()
	 * @method \int[] fillIblockId()
	 * @method \int[] getParentIdList()
	 * @method \int[] fillParentId()
	 * @method \boolean[] getStatusList()
	 * @method \boolean[] fillStatus()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object $object)
	 * @method bool has(\Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object getByPrimary($primary)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object[] getAll()
	 * @method bool remove(\Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO__Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO__Collection merge(?EO__Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO__Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Feed\Engine\Steps\Offer\Table */
		static public $dataClass = '\Avito\Export\Feed\Engine\Steps\Offer\Table';
	}
}
namespace Avito\Export\Feed\Engine\Steps\Offer {
	/**
	 * @method static EO__Query query()
	 * @method static EO__Result getByPrimary($primary, array $parameters = [])
	 * @method static EO__Result getById($id)
	 * @method static EO__Result getList(array $parameters = [])
	 * @method static EO__Entity getEntity()
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO__Collection createCollection()
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object wakeUpObject($row)
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO__Collection wakeUpCollection($rows)
	 */
	class Table extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO__Result exec()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object fetchObject()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO__Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO__Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object fetchObject()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO__Collection fetchCollection()
	 */
	class EO__Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object createObject($setDefaultValues = true)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO__Collection createCollection()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_NNM_Object wakeUpObject($row)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO__Collection wakeUpCollection($rows)
	 */
	class EO__Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Feed\Engine\Steps\Offer\CategoryLimitTable */
namespace Avito\Export\Feed\Engine\Steps\Offer {
	/**
	 * EO_CategoryLimit
	 * @see \Avito\Export\Feed\Engine\Steps\Offer\CategoryLimitTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getFeedId()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit setFeedId(\int|\Bitrix\Main\DB\SqlExpression $feedId)
	 * @method bool hasFeedId()
	 * @method bool isFeedIdFilled()
	 * @method bool isFeedIdChanged()
	 * @method \string getIndex()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit setIndex(\string|\Bitrix\Main\DB\SqlExpression $index)
	 * @method bool hasIndex()
	 * @method bool isIndexFilled()
	 * @method bool isIndexChanged()
	 * @method \int getPrimary()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit setPrimary(\int|\Bitrix\Main\DB\SqlExpression $primary)
	 * @method bool hasPrimary()
	 * @method bool isPrimaryFilled()
	 * @method bool isPrimaryChanged()
	 * @method \int getPriority()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit setPriority(\int|\Bitrix\Main\DB\SqlExpression $priority)
	 * @method bool hasPriority()
	 * @method bool isPriorityFilled()
	 * @method bool isPriorityChanged()
	 * @method \int remindActualPriority()
	 * @method \int requirePriority()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit resetPriority()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit unsetPriority()
	 * @method \int fillPriority()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit set($fieldName, $value)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit reset($fieldName)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit wakeUp($data)
	 */
	class EO_CategoryLimit {
		/* @var \Avito\Export\Feed\Engine\Steps\Offer\CategoryLimitTable */
		static public $dataClass = '\Avito\Export\Feed\Engine\Steps\Offer\CategoryLimitTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Feed\Engine\Steps\Offer {
	/**
	 * EO_CategoryLimit_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getFeedIdList()
	 * @method \string[] getIndexList()
	 * @method \int[] getPrimaryList()
	 * @method \int[] getPriorityList()
	 * @method \int[] fillPriority()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit $object)
	 * @method bool has(\Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit getByPrimary($primary)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit[] getAll()
	 * @method bool remove(\Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_CategoryLimit_Collection merge(?EO_CategoryLimit_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_CategoryLimit_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Feed\Engine\Steps\Offer\CategoryLimitTable */
		static public $dataClass = '\Avito\Export\Feed\Engine\Steps\Offer\CategoryLimitTable';
	}
}
namespace Avito\Export\Feed\Engine\Steps\Offer {
	/**
	 * @method static EO_CategoryLimit_Query query()
	 * @method static EO_CategoryLimit_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_CategoryLimit_Result getById($id)
	 * @method static EO_CategoryLimit_Result getList(array $parameters = [])
	 * @method static EO_CategoryLimit_Entity getEntity()
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit_Collection createCollection()
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit wakeUpObject($row)
	 * @method static \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit_Collection wakeUpCollection($rows)
	 */
	class CategoryLimitTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_CategoryLimit_Result exec()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit fetchObject()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_CategoryLimit_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit fetchObject()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit_Collection fetchCollection()
	 */
	class EO_CategoryLimit_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit createObject($setDefaultValues = true)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit_Collection createCollection()
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit wakeUpObject($row)
	 * @method \Avito\Export\Feed\Engine\Steps\Offer\EO_CategoryLimit_Collection wakeUpCollection($rows)
	 */
	class EO_CategoryLimit_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Feed\Logger\Table */
namespace Avito\Export\Feed\Logger {
	/**
	 * EO_NNM_Object
	 * @see \Avito\Export\Feed\Logger\Table
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getSetupType()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setSetupType(\string|\Bitrix\Main\DB\SqlExpression $setupType)
	 * @method bool hasSetupType()
	 * @method bool isSetupTypeFilled()
	 * @method bool isSetupTypeChanged()
	 * @method \int getSetupId()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setSetupId(\int|\Bitrix\Main\DB\SqlExpression $setupId)
	 * @method bool hasSetupId()
	 * @method bool isSetupIdFilled()
	 * @method bool isSetupIdChanged()
	 * @method \string getSign()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setSign(\string|\Bitrix\Main\DB\SqlExpression $sign)
	 * @method bool hasSign()
	 * @method bool isSignFilled()
	 * @method bool isSignChanged()
	 * @method \string getEntityType()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setEntityType(\string|\Bitrix\Main\DB\SqlExpression $entityType)
	 * @method bool hasEntityType()
	 * @method bool isEntityTypeFilled()
	 * @method bool isEntityTypeChanged()
	 * @method \string remindActualEntityType()
	 * @method \string requireEntityType()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object resetEntityType()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object unsetEntityType()
	 * @method \string fillEntityType()
	 * @method \string getEntityId()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setEntityId(\string|\Bitrix\Main\DB\SqlExpression $entityId)
	 * @method bool hasEntityId()
	 * @method bool isEntityIdFilled()
	 * @method bool isEntityIdChanged()
	 * @method \string remindActualEntityId()
	 * @method \string requireEntityId()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object resetEntityId()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object unsetEntityId()
	 * @method \string fillEntityId()
	 * @method \int getRegionId()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setRegionId(\int|\Bitrix\Main\DB\SqlExpression $regionId)
	 * @method bool hasRegionId()
	 * @method bool isRegionIdFilled()
	 * @method bool isRegionIdChanged()
	 * @method \int remindActualRegionId()
	 * @method \int requireRegionId()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object resetRegionId()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object unsetRegionId()
	 * @method \int fillRegionId()
	 * @method \string getLevel()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setLevel(\string|\Bitrix\Main\DB\SqlExpression $level)
	 * @method bool hasLevel()
	 * @method bool isLevelFilled()
	 * @method bool isLevelChanged()
	 * @method \string remindActualLevel()
	 * @method \string requireLevel()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object resetLevel()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object unsetLevel()
	 * @method \string fillLevel()
	 * @method \string getMessage()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setMessage(\string|\Bitrix\Main\DB\SqlExpression $message)
	 * @method bool hasMessage()
	 * @method bool isMessageFilled()
	 * @method bool isMessageChanged()
	 * @method \string remindActualMessage()
	 * @method \string requireMessage()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object resetMessage()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object unsetMessage()
	 * @method \string fillMessage()
	 * @method array getContext()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setContext(array|\Bitrix\Main\DB\SqlExpression $context)
	 * @method bool hasContext()
	 * @method bool isContextFilled()
	 * @method bool isContextChanged()
	 * @method array remindActualContext()
	 * @method array requireContext()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object resetContext()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object unsetContext()
	 * @method array fillContext()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object resetTimestampX()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object set($fieldName, $value)
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object reset($fieldName)
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Feed\Logger\EO_NNM_Object wakeUp($data)
	 */
	class EO_NNM_Object {
		/* @var \Avito\Export\Feed\Logger\Table */
		static public $dataClass = '\Avito\Export\Feed\Logger\Table';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Feed\Logger {
	/**
	 * EO__Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getSetupTypeList()
	 * @method \int[] getSetupIdList()
	 * @method \string[] getSignList()
	 * @method \string[] getEntityTypeList()
	 * @method \string[] fillEntityType()
	 * @method \string[] getEntityIdList()
	 * @method \string[] fillEntityId()
	 * @method \int[] getRegionIdList()
	 * @method \int[] fillRegionId()
	 * @method \string[] getLevelList()
	 * @method \string[] fillLevel()
	 * @method \string[] getMessageList()
	 * @method \string[] fillMessage()
	 * @method array[] getContextList()
	 * @method array[] fillContext()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Feed\Logger\EO_NNM_Object $object)
	 * @method bool has(\Avito\Export\Feed\Logger\EO_NNM_Object $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object getByPrimary($primary)
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object[] getAll()
	 * @method bool remove(\Avito\Export\Feed\Logger\EO_NNM_Object $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Feed\Logger\EO__Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO__Collection merge(?EO__Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO__Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Feed\Logger\Table */
		static public $dataClass = '\Avito\Export\Feed\Logger\Table';
	}
}
namespace Avito\Export\Feed\Logger {
	/**
	 * @method static EO__Query query()
	 * @method static EO__Result getByPrimary($primary, array $parameters = [])
	 * @method static EO__Result getById($id)
	 * @method static EO__Result getList(array $parameters = [])
	 * @method static EO__Entity getEntity()
	 * @method static \Avito\Export\Feed\Logger\EO_NNM_Object createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Feed\Logger\EO__Collection createCollection()
	 * @method static \Avito\Export\Feed\Logger\EO_NNM_Object wakeUpObject($row)
	 * @method static \Avito\Export\Feed\Logger\EO__Collection wakeUpCollection($rows)
	 */
	class Table extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO__Result exec()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object fetchObject()
	 * @method \Avito\Export\Feed\Logger\EO__Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO__Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object fetchObject()
	 * @method \Avito\Export\Feed\Logger\EO__Collection fetchCollection()
	 */
	class EO__Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object createObject($setDefaultValues = true)
	 * @method \Avito\Export\Feed\Logger\EO__Collection createCollection()
	 * @method \Avito\Export\Feed\Logger\EO_NNM_Object wakeUpObject($row)
	 * @method \Avito\Export\Feed\Logger\EO__Collection wakeUpCollection($rows)
	 */
	class EO__Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Push\Agent\StateTable */
namespace Avito\Export\Push\Agent {
	/**
	 * EO_State
	 * @see \Avito\Export\Push\Agent\StateTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getSetupType()
	 * @method \Avito\Export\Push\Agent\EO_State setSetupType(\string|\Bitrix\Main\DB\SqlExpression $setupType)
	 * @method bool hasSetupType()
	 * @method bool isSetupTypeFilled()
	 * @method bool isSetupTypeChanged()
	 * @method \int getSetupId()
	 * @method \Avito\Export\Push\Agent\EO_State setSetupId(\int|\Bitrix\Main\DB\SqlExpression $setupId)
	 * @method bool hasSetupId()
	 * @method bool isSetupIdFilled()
	 * @method bool isSetupIdChanged()
	 * @method \string getMethod()
	 * @method \Avito\Export\Push\Agent\EO_State setMethod(\string|\Bitrix\Main\DB\SqlExpression $method)
	 * @method bool hasMethod()
	 * @method bool isMethodFilled()
	 * @method bool isMethodChanged()
	 * @method \string getStep()
	 * @method \Avito\Export\Push\Agent\EO_State setStep(\string|\Bitrix\Main\DB\SqlExpression $step)
	 * @method bool hasStep()
	 * @method bool isStepFilled()
	 * @method bool isStepChanged()
	 * @method \string remindActualStep()
	 * @method \string requireStep()
	 * @method \Avito\Export\Push\Agent\EO_State resetStep()
	 * @method \Avito\Export\Push\Agent\EO_State unsetStep()
	 * @method \string fillStep()
	 * @method \string getOffset()
	 * @method \Avito\Export\Push\Agent\EO_State setOffset(\string|\Bitrix\Main\DB\SqlExpression $offset)
	 * @method bool hasOffset()
	 * @method bool isOffsetFilled()
	 * @method bool isOffsetChanged()
	 * @method \string remindActualOffset()
	 * @method \string requireOffset()
	 * @method \Avito\Export\Push\Agent\EO_State resetOffset()
	 * @method \Avito\Export\Push\Agent\EO_State unsetOffset()
	 * @method \string fillOffset()
	 * @method \Bitrix\Main\Type\DateTime getInitTime()
	 * @method \Avito\Export\Push\Agent\EO_State setInitTime(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $initTime)
	 * @method bool hasInitTime()
	 * @method bool isInitTimeFilled()
	 * @method bool isInitTimeChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualInitTime()
	 * @method \Bitrix\Main\Type\DateTime requireInitTime()
	 * @method \Avito\Export\Push\Agent\EO_State resetInitTime()
	 * @method \Avito\Export\Push\Agent\EO_State unsetInitTime()
	 * @method \Bitrix\Main\Type\DateTime fillInitTime()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Push\Agent\EO_State set($fieldName, $value)
	 * @method \Avito\Export\Push\Agent\EO_State reset($fieldName)
	 * @method \Avito\Export\Push\Agent\EO_State unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Push\Agent\EO_State wakeUp($data)
	 */
	class EO_State {
		/* @var \Avito\Export\Push\Agent\StateTable */
		static public $dataClass = '\Avito\Export\Push\Agent\StateTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Push\Agent {
	/**
	 * EO_State_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getSetupTypeList()
	 * @method \int[] getSetupIdList()
	 * @method \string[] getMethodList()
	 * @method \string[] getStepList()
	 * @method \string[] fillStep()
	 * @method \string[] getOffsetList()
	 * @method \string[] fillOffset()
	 * @method \Bitrix\Main\Type\DateTime[] getInitTimeList()
	 * @method \Bitrix\Main\Type\DateTime[] fillInitTime()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Push\Agent\EO_State $object)
	 * @method bool has(\Avito\Export\Push\Agent\EO_State $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Push\Agent\EO_State getByPrimary($primary)
	 * @method \Avito\Export\Push\Agent\EO_State[] getAll()
	 * @method bool remove(\Avito\Export\Push\Agent\EO_State $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Push\Agent\EO_State_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Push\Agent\EO_State current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_State_Collection merge(?EO_State_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_State_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Push\Agent\StateTable */
		static public $dataClass = '\Avito\Export\Push\Agent\StateTable';
	}
}
namespace Avito\Export\Push\Agent {
	/**
	 * @method static EO_State_Query query()
	 * @method static EO_State_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_State_Result getById($id)
	 * @method static EO_State_Result getList(array $parameters = [])
	 * @method static EO_State_Entity getEntity()
	 * @method static \Avito\Export\Push\Agent\EO_State createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Push\Agent\EO_State_Collection createCollection()
	 * @method static \Avito\Export\Push\Agent\EO_State wakeUpObject($row)
	 * @method static \Avito\Export\Push\Agent\EO_State_Collection wakeUpCollection($rows)
	 */
	class StateTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_State_Result exec()
	 * @method \Avito\Export\Push\Agent\EO_State fetchObject()
	 * @method \Avito\Export\Push\Agent\EO_State_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_State_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Push\Agent\EO_State fetchObject()
	 * @method \Avito\Export\Push\Agent\EO_State_Collection fetchCollection()
	 */
	class EO_State_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Push\Agent\EO_State createObject($setDefaultValues = true)
	 * @method \Avito\Export\Push\Agent\EO_State_Collection createCollection()
	 * @method \Avito\Export\Push\Agent\EO_State wakeUpObject($row)
	 * @method \Avito\Export\Push\Agent\EO_State_Collection wakeUpCollection($rows)
	 */
	class EO_State_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Watcher\Agent\StateTable */
namespace Avito\Export\Watcher\Agent {
	/**
	 * EO_State
	 * @see \Avito\Export\Watcher\Agent\StateTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getSetupType()
	 * @method \Avito\Export\Watcher\Agent\EO_State setSetupType(\string|\Bitrix\Main\DB\SqlExpression $setupType)
	 * @method bool hasSetupType()
	 * @method bool isSetupTypeFilled()
	 * @method bool isSetupTypeChanged()
	 * @method \int getSetupId()
	 * @method \Avito\Export\Watcher\Agent\EO_State setSetupId(\int|\Bitrix\Main\DB\SqlExpression $setupId)
	 * @method bool hasSetupId()
	 * @method bool isSetupIdFilled()
	 * @method bool isSetupIdChanged()
	 * @method \string getMethod()
	 * @method \Avito\Export\Watcher\Agent\EO_State setMethod(\string|\Bitrix\Main\DB\SqlExpression $method)
	 * @method bool hasMethod()
	 * @method bool isMethodFilled()
	 * @method bool isMethodChanged()
	 * @method \string getStep()
	 * @method \Avito\Export\Watcher\Agent\EO_State setStep(\string|\Bitrix\Main\DB\SqlExpression $step)
	 * @method bool hasStep()
	 * @method bool isStepFilled()
	 * @method bool isStepChanged()
	 * @method \string remindActualStep()
	 * @method \string requireStep()
	 * @method \Avito\Export\Watcher\Agent\EO_State resetStep()
	 * @method \Avito\Export\Watcher\Agent\EO_State unsetStep()
	 * @method \string fillStep()
	 * @method \string getOffset()
	 * @method \Avito\Export\Watcher\Agent\EO_State setOffset(\string|\Bitrix\Main\DB\SqlExpression $offset)
	 * @method bool hasOffset()
	 * @method bool isOffsetFilled()
	 * @method bool isOffsetChanged()
	 * @method \string remindActualOffset()
	 * @method \string requireOffset()
	 * @method \Avito\Export\Watcher\Agent\EO_State resetOffset()
	 * @method \Avito\Export\Watcher\Agent\EO_State unsetOffset()
	 * @method \string fillOffset()
	 * @method \Bitrix\Main\Type\DateTime getInitTime()
	 * @method \Avito\Export\Watcher\Agent\EO_State setInitTime(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $initTime)
	 * @method bool hasInitTime()
	 * @method bool isInitTimeFilled()
	 * @method bool isInitTimeChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualInitTime()
	 * @method \Bitrix\Main\Type\DateTime requireInitTime()
	 * @method \Avito\Export\Watcher\Agent\EO_State resetInitTime()
	 * @method \Avito\Export\Watcher\Agent\EO_State unsetInitTime()
	 * @method \Bitrix\Main\Type\DateTime fillInitTime()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Watcher\Agent\EO_State set($fieldName, $value)
	 * @method \Avito\Export\Watcher\Agent\EO_State reset($fieldName)
	 * @method \Avito\Export\Watcher\Agent\EO_State unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Watcher\Agent\EO_State wakeUp($data)
	 */
	class EO_State {
		/* @var \Avito\Export\Watcher\Agent\StateTable */
		static public $dataClass = '\Avito\Export\Watcher\Agent\StateTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Watcher\Agent {
	/**
	 * EO_State_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getSetupTypeList()
	 * @method \int[] getSetupIdList()
	 * @method \string[] getMethodList()
	 * @method \string[] getStepList()
	 * @method \string[] fillStep()
	 * @method \string[] getOffsetList()
	 * @method \string[] fillOffset()
	 * @method \Bitrix\Main\Type\DateTime[] getInitTimeList()
	 * @method \Bitrix\Main\Type\DateTime[] fillInitTime()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Watcher\Agent\EO_State $object)
	 * @method bool has(\Avito\Export\Watcher\Agent\EO_State $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Watcher\Agent\EO_State getByPrimary($primary)
	 * @method \Avito\Export\Watcher\Agent\EO_State[] getAll()
	 * @method bool remove(\Avito\Export\Watcher\Agent\EO_State $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Watcher\Agent\EO_State_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Watcher\Agent\EO_State current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_State_Collection merge(?EO_State_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_State_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Watcher\Agent\StateTable */
		static public $dataClass = '\Avito\Export\Watcher\Agent\StateTable';
	}
}
namespace Avito\Export\Watcher\Agent {
	/**
	 * @method static EO_State_Query query()
	 * @method static EO_State_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_State_Result getById($id)
	 * @method static EO_State_Result getList(array $parameters = [])
	 * @method static EO_State_Entity getEntity()
	 * @method static \Avito\Export\Watcher\Agent\EO_State createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Watcher\Agent\EO_State_Collection createCollection()
	 * @method static \Avito\Export\Watcher\Agent\EO_State wakeUpObject($row)
	 * @method static \Avito\Export\Watcher\Agent\EO_State_Collection wakeUpCollection($rows)
	 */
	class StateTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_State_Result exec()
	 * @method \Avito\Export\Watcher\Agent\EO_State fetchObject()
	 * @method \Avito\Export\Watcher\Agent\EO_State_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_State_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Watcher\Agent\EO_State fetchObject()
	 * @method \Avito\Export\Watcher\Agent\EO_State_Collection fetchCollection()
	 */
	class EO_State_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Watcher\Agent\EO_State createObject($setDefaultValues = true)
	 * @method \Avito\Export\Watcher\Agent\EO_State_Collection createCollection()
	 * @method \Avito\Export\Watcher\Agent\EO_State wakeUpObject($row)
	 * @method \Avito\Export\Watcher\Agent\EO_State_Collection wakeUpCollection($rows)
	 */
	class EO_State_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Push\Agent\ChangesTable */
namespace Avito\Export\Push\Agent {
	/**
	 * EO_Changes
	 * @see \Avito\Export\Push\Agent\ChangesTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getSetupType()
	 * @method \Avito\Export\Push\Agent\EO_Changes setSetupType(\string|\Bitrix\Main\DB\SqlExpression $setupType)
	 * @method bool hasSetupType()
	 * @method bool isSetupTypeFilled()
	 * @method bool isSetupTypeChanged()
	 * @method \int getSetupId()
	 * @method \Avito\Export\Push\Agent\EO_Changes setSetupId(\int|\Bitrix\Main\DB\SqlExpression $setupId)
	 * @method bool hasSetupId()
	 * @method bool isSetupIdFilled()
	 * @method bool isSetupIdChanged()
	 * @method \string getEntityType()
	 * @method \Avito\Export\Push\Agent\EO_Changes setEntityType(\string|\Bitrix\Main\DB\SqlExpression $entityType)
	 * @method bool hasEntityType()
	 * @method bool isEntityTypeFilled()
	 * @method bool isEntityTypeChanged()
	 * @method \int getEntityId()
	 * @method \Avito\Export\Push\Agent\EO_Changes setEntityId(\int|\Bitrix\Main\DB\SqlExpression $entityId)
	 * @method bool hasEntityId()
	 * @method bool isEntityIdFilled()
	 * @method bool isEntityIdChanged()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Push\Agent\EO_Changes setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Push\Agent\EO_Changes resetTimestampX()
	 * @method \Avito\Export\Push\Agent\EO_Changes unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Push\Agent\EO_Changes set($fieldName, $value)
	 * @method \Avito\Export\Push\Agent\EO_Changes reset($fieldName)
	 * @method \Avito\Export\Push\Agent\EO_Changes unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Push\Agent\EO_Changes wakeUp($data)
	 */
	class EO_Changes {
		/* @var \Avito\Export\Push\Agent\ChangesTable */
		static public $dataClass = '\Avito\Export\Push\Agent\ChangesTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Push\Agent {
	/**
	 * EO_Changes_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getSetupTypeList()
	 * @method \int[] getSetupIdList()
	 * @method \string[] getEntityTypeList()
	 * @method \int[] getEntityIdList()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Push\Agent\EO_Changes $object)
	 * @method bool has(\Avito\Export\Push\Agent\EO_Changes $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Push\Agent\EO_Changes getByPrimary($primary)
	 * @method \Avito\Export\Push\Agent\EO_Changes[] getAll()
	 * @method bool remove(\Avito\Export\Push\Agent\EO_Changes $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Push\Agent\EO_Changes_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Push\Agent\EO_Changes current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Changes_Collection merge(?EO_Changes_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Changes_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Push\Agent\ChangesTable */
		static public $dataClass = '\Avito\Export\Push\Agent\ChangesTable';
	}
}
namespace Avito\Export\Push\Agent {
	/**
	 * @method static EO_Changes_Query query()
	 * @method static EO_Changes_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Changes_Result getById($id)
	 * @method static EO_Changes_Result getList(array $parameters = [])
	 * @method static EO_Changes_Entity getEntity()
	 * @method static \Avito\Export\Push\Agent\EO_Changes createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Push\Agent\EO_Changes_Collection createCollection()
	 * @method static \Avito\Export\Push\Agent\EO_Changes wakeUpObject($row)
	 * @method static \Avito\Export\Push\Agent\EO_Changes_Collection wakeUpCollection($rows)
	 */
	class ChangesTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Changes_Result exec()
	 * @method \Avito\Export\Push\Agent\EO_Changes fetchObject()
	 * @method \Avito\Export\Push\Agent\EO_Changes_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Changes_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Push\Agent\EO_Changes fetchObject()
	 * @method \Avito\Export\Push\Agent\EO_Changes_Collection fetchCollection()
	 */
	class EO_Changes_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Push\Agent\EO_Changes createObject($setDefaultValues = true)
	 * @method \Avito\Export\Push\Agent\EO_Changes_Collection createCollection()
	 * @method \Avito\Export\Push\Agent\EO_Changes wakeUpObject($row)
	 * @method \Avito\Export\Push\Agent\EO_Changes_Collection wakeUpCollection($rows)
	 */
	class EO_Changes_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Watcher\Agent\ChangesTable */
namespace Avito\Export\Watcher\Agent {
	/**
	 * EO_Changes
	 * @see \Avito\Export\Watcher\Agent\ChangesTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getSetupType()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes setSetupType(\string|\Bitrix\Main\DB\SqlExpression $setupType)
	 * @method bool hasSetupType()
	 * @method bool isSetupTypeFilled()
	 * @method bool isSetupTypeChanged()
	 * @method \int getSetupId()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes setSetupId(\int|\Bitrix\Main\DB\SqlExpression $setupId)
	 * @method bool hasSetupId()
	 * @method bool isSetupIdFilled()
	 * @method bool isSetupIdChanged()
	 * @method \string getEntityType()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes setEntityType(\string|\Bitrix\Main\DB\SqlExpression $entityType)
	 * @method bool hasEntityType()
	 * @method bool isEntityTypeFilled()
	 * @method bool isEntityTypeChanged()
	 * @method \int getEntityId()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes setEntityId(\int|\Bitrix\Main\DB\SqlExpression $entityId)
	 * @method bool hasEntityId()
	 * @method bool isEntityIdFilled()
	 * @method bool isEntityIdChanged()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes resetTimestampX()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Watcher\Agent\EO_Changes set($fieldName, $value)
	 * @method \Avito\Export\Watcher\Agent\EO_Changes reset($fieldName)
	 * @method \Avito\Export\Watcher\Agent\EO_Changes unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Watcher\Agent\EO_Changes wakeUp($data)
	 */
	class EO_Changes {
		/* @var \Avito\Export\Watcher\Agent\ChangesTable */
		static public $dataClass = '\Avito\Export\Watcher\Agent\ChangesTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Watcher\Agent {
	/**
	 * EO_Changes_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getSetupTypeList()
	 * @method \int[] getSetupIdList()
	 * @method \string[] getEntityTypeList()
	 * @method \int[] getEntityIdList()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Watcher\Agent\EO_Changes $object)
	 * @method bool has(\Avito\Export\Watcher\Agent\EO_Changes $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Watcher\Agent\EO_Changes getByPrimary($primary)
	 * @method \Avito\Export\Watcher\Agent\EO_Changes[] getAll()
	 * @method bool remove(\Avito\Export\Watcher\Agent\EO_Changes $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Watcher\Agent\EO_Changes_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Watcher\Agent\EO_Changes current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Changes_Collection merge(?EO_Changes_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Changes_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Watcher\Agent\ChangesTable */
		static public $dataClass = '\Avito\Export\Watcher\Agent\ChangesTable';
	}
}
namespace Avito\Export\Watcher\Agent {
	/**
	 * @method static EO_Changes_Query query()
	 * @method static EO_Changes_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Changes_Result getById($id)
	 * @method static EO_Changes_Result getList(array $parameters = [])
	 * @method static EO_Changes_Entity getEntity()
	 * @method static \Avito\Export\Watcher\Agent\EO_Changes createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Watcher\Agent\EO_Changes_Collection createCollection()
	 * @method static \Avito\Export\Watcher\Agent\EO_Changes wakeUpObject($row)
	 * @method static \Avito\Export\Watcher\Agent\EO_Changes_Collection wakeUpCollection($rows)
	 */
	class ChangesTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Changes_Result exec()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes fetchObject()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Changes_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Watcher\Agent\EO_Changes fetchObject()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes_Collection fetchCollection()
	 */
	class EO_Changes_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Watcher\Agent\EO_Changes createObject($setDefaultValues = true)
	 * @method \Avito\Export\Watcher\Agent\EO_Changes_Collection createCollection()
	 * @method \Avito\Export\Watcher\Agent\EO_Changes wakeUpObject($row)
	 * @method \Avito\Export\Watcher\Agent\EO_Changes_Collection wakeUpCollection($rows)
	 */
	class EO_Changes_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Feed\Setup\RepositoryTable */
namespace Avito\Export\Feed\Setup {
	/**
	 * Model
	 * @see \Avito\Export\Feed\Setup\RepositoryTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Avito\Export\Feed\Setup\Model setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \string getName()
	 * @method \Avito\Export\Feed\Setup\Model setName(\string|\Bitrix\Main\DB\SqlExpression $name)
	 * @method bool hasName()
	 * @method bool isNameFilled()
	 * @method bool isNameChanged()
	 * @method \string remindActualName()
	 * @method \string requireName()
	 * @method \Avito\Export\Feed\Setup\Model resetName()
	 * @method \Avito\Export\Feed\Setup\Model unsetName()
	 * @method \string fillName()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Feed\Setup\Model setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Feed\Setup\Model resetTimestampX()
	 * @method \Avito\Export\Feed\Setup\Model unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 * @method array getSite()
	 * @method \Avito\Export\Feed\Setup\Model setSite(array|\Bitrix\Main\DB\SqlExpression $site)
	 * @method bool hasSite()
	 * @method bool isSiteFilled()
	 * @method bool isSiteChanged()
	 * @method array remindActualSite()
	 * @method array requireSite()
	 * @method \Avito\Export\Feed\Setup\Model resetSite()
	 * @method \Avito\Export\Feed\Setup\Model unsetSite()
	 * @method array fillSite()
	 * @method \boolean getHttps()
	 * @method \Avito\Export\Feed\Setup\Model setHttps(\boolean|\Bitrix\Main\DB\SqlExpression $https)
	 * @method bool hasHttps()
	 * @method bool isHttpsFilled()
	 * @method bool isHttpsChanged()
	 * @method \boolean remindActualHttps()
	 * @method \boolean requireHttps()
	 * @method \Avito\Export\Feed\Setup\Model resetHttps()
	 * @method \Avito\Export\Feed\Setup\Model unsetHttps()
	 * @method \boolean fillHttps()
	 * @method array getIblock()
	 * @method \Avito\Export\Feed\Setup\Model setIblock(array|\Bitrix\Main\DB\SqlExpression $iblock)
	 * @method bool hasIblock()
	 * @method bool isIblockFilled()
	 * @method bool isIblockChanged()
	 * @method array remindActualIblock()
	 * @method array requireIblock()
	 * @method \Avito\Export\Feed\Setup\Model resetIblock()
	 * @method \Avito\Export\Feed\Setup\Model unsetIblock()
	 * @method array fillIblock()
	 * @method \int getRegion()
	 * @method \Avito\Export\Feed\Setup\Model setRegion(\int|\Bitrix\Main\DB\SqlExpression $region)
	 * @method bool hasRegion()
	 * @method bool isRegionFilled()
	 * @method bool isRegionChanged()
	 * @method \int remindActualRegion()
	 * @method \int requireRegion()
	 * @method \Avito\Export\Feed\Setup\Model resetRegion()
	 * @method \Avito\Export\Feed\Setup\Model unsetRegion()
	 * @method \int fillRegion()
	 * @method \string getFileName()
	 * @method \Avito\Export\Feed\Setup\Model setFileName(\string|\Bitrix\Main\DB\SqlExpression $fileName)
	 * @method bool hasFileName()
	 * @method bool isFileNameFilled()
	 * @method bool isFileNameChanged()
	 * @method \string remindActualFileName()
	 * @method \string requireFileName()
	 * @method \Avito\Export\Feed\Setup\Model resetFileName()
	 * @method \Avito\Export\Feed\Setup\Model unsetFileName()
	 * @method \string fillFileName()
	 * @method array getFilter()
	 * @method \Avito\Export\Feed\Setup\Model setFilter(array|\Bitrix\Main\DB\SqlExpression $filter)
	 * @method bool hasFilter()
	 * @method bool isFilterFilled()
	 * @method bool isFilterChanged()
	 * @method array remindActualFilter()
	 * @method array requireFilter()
	 * @method \Avito\Export\Feed\Setup\Model resetFilter()
	 * @method \Avito\Export\Feed\Setup\Model unsetFilter()
	 * @method array fillFilter()
	 * @method array getCategoryLimit()
	 * @method \Avito\Export\Feed\Setup\Model setCategoryLimit(array|\Bitrix\Main\DB\SqlExpression $categoryLimit)
	 * @method bool hasCategoryLimit()
	 * @method bool isCategoryLimitFilled()
	 * @method bool isCategoryLimitChanged()
	 * @method array remindActualCategoryLimit()
	 * @method array requireCategoryLimit()
	 * @method \Avito\Export\Feed\Setup\Model resetCategoryLimit()
	 * @method \Avito\Export\Feed\Setup\Model unsetCategoryLimit()
	 * @method array fillCategoryLimit()
	 * @method array getTags()
	 * @method \Avito\Export\Feed\Setup\Model setTags(array|\Bitrix\Main\DB\SqlExpression $tags)
	 * @method bool hasTags()
	 * @method bool isTagsFilled()
	 * @method bool isTagsChanged()
	 * @method array remindActualTags()
	 * @method array requireTags()
	 * @method \Avito\Export\Feed\Setup\Model resetTags()
	 * @method \Avito\Export\Feed\Setup\Model unsetTags()
	 * @method array fillTags()
	 * @method \boolean getAutoUpdate()
	 * @method \Avito\Export\Feed\Setup\Model setAutoUpdate(\boolean|\Bitrix\Main\DB\SqlExpression $autoUpdate)
	 * @method bool hasAutoUpdate()
	 * @method bool isAutoUpdateFilled()
	 * @method bool isAutoUpdateChanged()
	 * @method \boolean remindActualAutoUpdate()
	 * @method \boolean requireAutoUpdate()
	 * @method \Avito\Export\Feed\Setup\Model resetAutoUpdate()
	 * @method \Avito\Export\Feed\Setup\Model unsetAutoUpdate()
	 * @method \boolean fillAutoUpdate()
	 * @method \int getRefreshPeriod()
	 * @method \Avito\Export\Feed\Setup\Model setRefreshPeriod(\int|\Bitrix\Main\DB\SqlExpression $refreshPeriod)
	 * @method bool hasRefreshPeriod()
	 * @method bool isRefreshPeriodFilled()
	 * @method bool isRefreshPeriodChanged()
	 * @method \int remindActualRefreshPeriod()
	 * @method \int requireRefreshPeriod()
	 * @method \Avito\Export\Feed\Setup\Model resetRefreshPeriod()
	 * @method \Avito\Export\Feed\Setup\Model unsetRefreshPeriod()
	 * @method \int fillRefreshPeriod()
	 * @method \string getRefreshTime()
	 * @method \Avito\Export\Feed\Setup\Model setRefreshTime(\string|\Bitrix\Main\DB\SqlExpression $refreshTime)
	 * @method bool hasRefreshTime()
	 * @method bool isRefreshTimeFilled()
	 * @method bool isRefreshTimeChanged()
	 * @method \string remindActualRefreshTime()
	 * @method \string requireRefreshTime()
	 * @method \Avito\Export\Feed\Setup\Model resetRefreshTime()
	 * @method \Avito\Export\Feed\Setup\Model unsetRefreshTime()
	 * @method \string fillRefreshTime()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Feed\Setup\Model set($fieldName, $value)
	 * @method \Avito\Export\Feed\Setup\Model reset($fieldName)
	 * @method \Avito\Export\Feed\Setup\Model unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Feed\Setup\Model wakeUp($data)
	 */
	class EO_Repository {
		/* @var \Avito\Export\Feed\Setup\RepositoryTable */
		static public $dataClass = '\Avito\Export\Feed\Setup\RepositoryTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Feed\Setup {
	/**
	 * EO_Repository_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \string[] getNameList()
	 * @method \string[] fillName()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 * @method array[] getSiteList()
	 * @method array[] fillSite()
	 * @method \boolean[] getHttpsList()
	 * @method \boolean[] fillHttps()
	 * @method array[] getIblockList()
	 * @method array[] fillIblock()
	 * @method \int[] getRegionList()
	 * @method \int[] fillRegion()
	 * @method \string[] getFileNameList()
	 * @method \string[] fillFileName()
	 * @method array[] getFilterList()
	 * @method array[] fillFilter()
	 * @method array[] getCategoryLimitList()
	 * @method array[] fillCategoryLimit()
	 * @method array[] getTagsList()
	 * @method array[] fillTags()
	 * @method \boolean[] getAutoUpdateList()
	 * @method \boolean[] fillAutoUpdate()
	 * @method \int[] getRefreshPeriodList()
	 * @method \int[] fillRefreshPeriod()
	 * @method \string[] getRefreshTimeList()
	 * @method \string[] fillRefreshTime()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Feed\Setup\Model $object)
	 * @method bool has(\Avito\Export\Feed\Setup\Model $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Feed\Setup\Model getByPrimary($primary)
	 * @method \Avito\Export\Feed\Setup\Model[] getAll()
	 * @method bool remove(\Avito\Export\Feed\Setup\Model $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Feed\Setup\EO_Repository_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Feed\Setup\Model current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Repository_Collection merge(?EO_Repository_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Repository_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Feed\Setup\RepositoryTable */
		static public $dataClass = '\Avito\Export\Feed\Setup\RepositoryTable';
	}
}
namespace Avito\Export\Feed\Setup {
	/**
	 * @method static EO_Repository_Query query()
	 * @method static EO_Repository_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Repository_Result getById($id)
	 * @method static EO_Repository_Result getList(array $parameters = [])
	 * @method static EO_Repository_Entity getEntity()
	 * @method static \Avito\Export\Feed\Setup\Model createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Feed\Setup\EO_Repository_Collection createCollection()
	 * @method static \Avito\Export\Feed\Setup\Model wakeUpObject($row)
	 * @method static \Avito\Export\Feed\Setup\EO_Repository_Collection wakeUpCollection($rows)
	 */
	class RepositoryTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Repository_Result exec()
	 * @method \Avito\Export\Feed\Setup\Model fetchObject()
	 * @method \Avito\Export\Feed\Setup\EO_Repository_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Repository_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Feed\Setup\Model fetchObject()
	 * @method \Avito\Export\Feed\Setup\EO_Repository_Collection fetchCollection()
	 */
	class EO_Repository_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Feed\Setup\Model createObject($setDefaultValues = true)
	 * @method \Avito\Export\Feed\Setup\EO_Repository_Collection createCollection()
	 * @method \Avito\Export\Feed\Setup\Model wakeUpObject($row)
	 * @method \Avito\Export\Feed\Setup\EO_Repository_Collection wakeUpCollection($rows)
	 */
	class EO_Repository_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Watcher\RegistryTable */
namespace Avito\Export\Watcher {
	/**
	 * EO_Registry
	 * @see \Avito\Export\Watcher\RegistryTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Avito\Export\Watcher\EO_Registry setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \string getEntityType()
	 * @method \Avito\Export\Watcher\EO_Registry setEntityType(\string|\Bitrix\Main\DB\SqlExpression $entityType)
	 * @method bool hasEntityType()
	 * @method bool isEntityTypeFilled()
	 * @method bool isEntityTypeChanged()
	 * @method \string remindActualEntityType()
	 * @method \string requireEntityType()
	 * @method \Avito\Export\Watcher\EO_Registry resetEntityType()
	 * @method \Avito\Export\Watcher\EO_Registry unsetEntityType()
	 * @method \string fillEntityType()
	 * @method \int getEntityId()
	 * @method \Avito\Export\Watcher\EO_Registry setEntityId(\int|\Bitrix\Main\DB\SqlExpression $entityId)
	 * @method bool hasEntityId()
	 * @method bool isEntityIdFilled()
	 * @method bool isEntityIdChanged()
	 * @method \int remindActualEntityId()
	 * @method \int requireEntityId()
	 * @method \Avito\Export\Watcher\EO_Registry resetEntityId()
	 * @method \Avito\Export\Watcher\EO_Registry unsetEntityId()
	 * @method \int fillEntityId()
	 * @method \int getIblockId()
	 * @method \Avito\Export\Watcher\EO_Registry setIblockId(\int|\Bitrix\Main\DB\SqlExpression $iblockId)
	 * @method bool hasIblockId()
	 * @method bool isIblockIdFilled()
	 * @method bool isIblockIdChanged()
	 * @method \int remindActualIblockId()
	 * @method \int requireIblockId()
	 * @method \Avito\Export\Watcher\EO_Registry resetIblockId()
	 * @method \Avito\Export\Watcher\EO_Registry unsetIblockId()
	 * @method \int fillIblockId()
	 * @method \string getSource()
	 * @method \Avito\Export\Watcher\EO_Registry setSource(\string|\Bitrix\Main\DB\SqlExpression $source)
	 * @method bool hasSource()
	 * @method bool isSourceFilled()
	 * @method bool isSourceChanged()
	 * @method \string remindActualSource()
	 * @method \string requireSource()
	 * @method \Avito\Export\Watcher\EO_Registry resetSource()
	 * @method \Avito\Export\Watcher\EO_Registry unsetSource()
	 * @method \string fillSource()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Watcher\EO_Registry set($fieldName, $value)
	 * @method \Avito\Export\Watcher\EO_Registry reset($fieldName)
	 * @method \Avito\Export\Watcher\EO_Registry unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Watcher\EO_Registry wakeUp($data)
	 */
	class EO_Registry {
		/* @var \Avito\Export\Watcher\RegistryTable */
		static public $dataClass = '\Avito\Export\Watcher\RegistryTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Watcher {
	/**
	 * EO_Registry_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \string[] getEntityTypeList()
	 * @method \string[] fillEntityType()
	 * @method \int[] getEntityIdList()
	 * @method \int[] fillEntityId()
	 * @method \int[] getIblockIdList()
	 * @method \int[] fillIblockId()
	 * @method \string[] getSourceList()
	 * @method \string[] fillSource()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Watcher\EO_Registry $object)
	 * @method bool has(\Avito\Export\Watcher\EO_Registry $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Watcher\EO_Registry getByPrimary($primary)
	 * @method \Avito\Export\Watcher\EO_Registry[] getAll()
	 * @method bool remove(\Avito\Export\Watcher\EO_Registry $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Watcher\EO_Registry_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Watcher\EO_Registry current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Registry_Collection merge(?EO_Registry_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Registry_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Watcher\RegistryTable */
		static public $dataClass = '\Avito\Export\Watcher\RegistryTable';
	}
}
namespace Avito\Export\Watcher {
	/**
	 * @method static EO_Registry_Query query()
	 * @method static EO_Registry_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Registry_Result getById($id)
	 * @method static EO_Registry_Result getList(array $parameters = [])
	 * @method static EO_Registry_Entity getEntity()
	 * @method static \Avito\Export\Watcher\EO_Registry createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Watcher\EO_Registry_Collection createCollection()
	 * @method static \Avito\Export\Watcher\EO_Registry wakeUpObject($row)
	 * @method static \Avito\Export\Watcher\EO_Registry_Collection wakeUpCollection($rows)
	 */
	class RegistryTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Registry_Result exec()
	 * @method \Avito\Export\Watcher\EO_Registry fetchObject()
	 * @method \Avito\Export\Watcher\EO_Registry_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Registry_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Watcher\EO_Registry fetchObject()
	 * @method \Avito\Export\Watcher\EO_Registry_Collection fetchCollection()
	 */
	class EO_Registry_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Watcher\EO_Registry createObject($setDefaultValues = true)
	 * @method \Avito\Export\Watcher\EO_Registry_Collection createCollection()
	 * @method \Avito\Export\Watcher\EO_Registry wakeUpObject($row)
	 * @method \Avito\Export\Watcher\EO_Registry_Collection wakeUpCollection($rows)
	 */
	class EO_Registry_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Chat\Unread\MessageTable */
namespace Avito\Export\Chat\Unread {
	/**
	 * Message
	 * @see \Avito\Export\Chat\Unread\MessageTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getExternalId()
	 * @method \Avito\Export\Chat\Unread\Message setExternalId(\string|\Bitrix\Main\DB\SqlExpression $externalId)
	 * @method bool hasExternalId()
	 * @method bool isExternalIdFilled()
	 * @method bool isExternalIdChanged()
	 * @method \int getSetupId()
	 * @method \Avito\Export\Chat\Unread\Message setSetupId(\int|\Bitrix\Main\DB\SqlExpression $setupId)
	 * @method bool hasSetupId()
	 * @method bool isSetupIdFilled()
	 * @method bool isSetupIdChanged()
	 * @method \int remindActualSetupId()
	 * @method \int requireSetupId()
	 * @method \Avito\Export\Chat\Unread\Message resetSetupId()
	 * @method \Avito\Export\Chat\Unread\Message unsetSetupId()
	 * @method \int fillSetupId()
	 * @method \string getChatId()
	 * @method \Avito\Export\Chat\Unread\Message setChatId(\string|\Bitrix\Main\DB\SqlExpression $chatId)
	 * @method bool hasChatId()
	 * @method bool isChatIdFilled()
	 * @method bool isChatIdChanged()
	 * @method \string remindActualChatId()
	 * @method \string requireChatId()
	 * @method \Avito\Export\Chat\Unread\Message resetChatId()
	 * @method \Avito\Export\Chat\Unread\Message unsetChatId()
	 * @method \string fillChatId()
	 * @method \int getAuthorId()
	 * @method \Avito\Export\Chat\Unread\Message setAuthorId(\int|\Bitrix\Main\DB\SqlExpression $authorId)
	 * @method bool hasAuthorId()
	 * @method bool isAuthorIdFilled()
	 * @method bool isAuthorIdChanged()
	 * @method \int remindActualAuthorId()
	 * @method \int requireAuthorId()
	 * @method \Avito\Export\Chat\Unread\Message resetAuthorId()
	 * @method \Avito\Export\Chat\Unread\Message unsetAuthorId()
	 * @method \int fillAuthorId()
	 * @method \string getChatType()
	 * @method \Avito\Export\Chat\Unread\Message setChatType(\string|\Bitrix\Main\DB\SqlExpression $chatType)
	 * @method bool hasChatType()
	 * @method bool isChatTypeFilled()
	 * @method bool isChatTypeChanged()
	 * @method \string remindActualChatType()
	 * @method \string requireChatType()
	 * @method \Avito\Export\Chat\Unread\Message resetChatType()
	 * @method \Avito\Export\Chat\Unread\Message unsetChatType()
	 * @method \string fillChatType()
	 * @method array getContent()
	 * @method \Avito\Export\Chat\Unread\Message setContent(array|\Bitrix\Main\DB\SqlExpression $content)
	 * @method bool hasContent()
	 * @method bool isContentFilled()
	 * @method bool isContentChanged()
	 * @method array remindActualContent()
	 * @method array requireContent()
	 * @method \Avito\Export\Chat\Unread\Message resetContent()
	 * @method \Avito\Export\Chat\Unread\Message unsetContent()
	 * @method array fillContent()
	 * @method \Bitrix\Main\Type\DateTime getCreated()
	 * @method \Avito\Export\Chat\Unread\Message setCreated(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $created)
	 * @method bool hasCreated()
	 * @method bool isCreatedFilled()
	 * @method bool isCreatedChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualCreated()
	 * @method \Bitrix\Main\Type\DateTime requireCreated()
	 * @method \Avito\Export\Chat\Unread\Message resetCreated()
	 * @method \Avito\Export\Chat\Unread\Message unsetCreated()
	 * @method \Bitrix\Main\Type\DateTime fillCreated()
	 * @method \int getItemId()
	 * @method \Avito\Export\Chat\Unread\Message setItemId(\int|\Bitrix\Main\DB\SqlExpression $itemId)
	 * @method bool hasItemId()
	 * @method bool isItemIdFilled()
	 * @method bool isItemIdChanged()
	 * @method \int remindActualItemId()
	 * @method \int requireItemId()
	 * @method \Avito\Export\Chat\Unread\Message resetItemId()
	 * @method \Avito\Export\Chat\Unread\Message unsetItemId()
	 * @method \int fillItemId()
	 * @method \Bitrix\Main\Type\DateTime getRead()
	 * @method \Avito\Export\Chat\Unread\Message setRead(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $read)
	 * @method bool hasRead()
	 * @method bool isReadFilled()
	 * @method bool isReadChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualRead()
	 * @method \Bitrix\Main\Type\DateTime requireRead()
	 * @method \Avito\Export\Chat\Unread\Message resetRead()
	 * @method \Avito\Export\Chat\Unread\Message unsetRead()
	 * @method \Bitrix\Main\Type\DateTime fillRead()
	 * @method \string getType()
	 * @method \Avito\Export\Chat\Unread\Message setType(\string|\Bitrix\Main\DB\SqlExpression $type)
	 * @method bool hasType()
	 * @method bool isTypeFilled()
	 * @method bool isTypeChanged()
	 * @method \string remindActualType()
	 * @method \string requireType()
	 * @method \Avito\Export\Chat\Unread\Message resetType()
	 * @method \Avito\Export\Chat\Unread\Message unsetType()
	 * @method \string fillType()
	 * @method \int getUserId()
	 * @method \Avito\Export\Chat\Unread\Message setUserId(\int|\Bitrix\Main\DB\SqlExpression $userId)
	 * @method bool hasUserId()
	 * @method bool isUserIdFilled()
	 * @method bool isUserIdChanged()
	 * @method \int remindActualUserId()
	 * @method \int requireUserId()
	 * @method \Avito\Export\Chat\Unread\Message resetUserId()
	 * @method \Avito\Export\Chat\Unread\Message unsetUserId()
	 * @method \int fillUserId()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Chat\Unread\Message set($fieldName, $value)
	 * @method \Avito\Export\Chat\Unread\Message reset($fieldName)
	 * @method \Avito\Export\Chat\Unread\Message unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Chat\Unread\Message wakeUp($data)
	 */
	class EO_Message {
		/* @var \Avito\Export\Chat\Unread\MessageTable */
		static public $dataClass = '\Avito\Export\Chat\Unread\MessageTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Chat\Unread {
	/**
	 * EO_Message_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getExternalIdList()
	 * @method \int[] getSetupIdList()
	 * @method \int[] fillSetupId()
	 * @method \string[] getChatIdList()
	 * @method \string[] fillChatId()
	 * @method \int[] getAuthorIdList()
	 * @method \int[] fillAuthorId()
	 * @method \string[] getChatTypeList()
	 * @method \string[] fillChatType()
	 * @method array[] getContentList()
	 * @method array[] fillContent()
	 * @method \Bitrix\Main\Type\DateTime[] getCreatedList()
	 * @method \Bitrix\Main\Type\DateTime[] fillCreated()
	 * @method \int[] getItemIdList()
	 * @method \int[] fillItemId()
	 * @method \Bitrix\Main\Type\DateTime[] getReadList()
	 * @method \Bitrix\Main\Type\DateTime[] fillRead()
	 * @method \string[] getTypeList()
	 * @method \string[] fillType()
	 * @method \int[] getUserIdList()
	 * @method \int[] fillUserId()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Chat\Unread\Message $object)
	 * @method bool has(\Avito\Export\Chat\Unread\Message $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Chat\Unread\Message getByPrimary($primary)
	 * @method \Avito\Export\Chat\Unread\Message[] getAll()
	 * @method bool remove(\Avito\Export\Chat\Unread\Message $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Chat\Unread\EO_Message_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Chat\Unread\Message current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Message_Collection merge(?EO_Message_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Message_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Chat\Unread\MessageTable */
		static public $dataClass = '\Avito\Export\Chat\Unread\MessageTable';
	}
}
namespace Avito\Export\Chat\Unread {
	/**
	 * @method static EO_Message_Query query()
	 * @method static EO_Message_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Message_Result getById($id)
	 * @method static EO_Message_Result getList(array $parameters = [])
	 * @method static EO_Message_Entity getEntity()
	 * @method static \Avito\Export\Chat\Unread\Message createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Chat\Unread\EO_Message_Collection createCollection()
	 * @method static \Avito\Export\Chat\Unread\Message wakeUpObject($row)
	 * @method static \Avito\Export\Chat\Unread\EO_Message_Collection wakeUpCollection($rows)
	 */
	class MessageTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Message_Result exec()
	 * @method \Avito\Export\Chat\Unread\Message fetchObject()
	 * @method \Avito\Export\Chat\Unread\EO_Message_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Message_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Chat\Unread\Message fetchObject()
	 * @method \Avito\Export\Chat\Unread\EO_Message_Collection fetchCollection()
	 */
	class EO_Message_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Chat\Unread\Message createObject($setDefaultValues = true)
	 * @method \Avito\Export\Chat\Unread\EO_Message_Collection createCollection()
	 * @method \Avito\Export\Chat\Unread\Message wakeUpObject($row)
	 * @method \Avito\Export\Chat\Unread\EO_Message_Collection wakeUpCollection($rows)
	 */
	class EO_Message_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Exchange\Setup\RepositoryTable */
namespace Avito\Export\Exchange\Setup {
	/**
	 * Model
	 * @see \Avito\Export\Exchange\Setup\RepositoryTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \Avito\Export\Exchange\Setup\Model setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \string getName()
	 * @method \Avito\Export\Exchange\Setup\Model setName(\string|\Bitrix\Main\DB\SqlExpression $name)
	 * @method bool hasName()
	 * @method bool isNameFilled()
	 * @method bool isNameChanged()
	 * @method \string remindActualName()
	 * @method \string requireName()
	 * @method \Avito\Export\Exchange\Setup\Model resetName()
	 * @method \Avito\Export\Exchange\Setup\Model unsetName()
	 * @method \string fillName()
	 * @method \int getFeedId()
	 * @method \Avito\Export\Exchange\Setup\Model setFeedId(\int|\Bitrix\Main\DB\SqlExpression $feedId)
	 * @method bool hasFeedId()
	 * @method bool isFeedIdFilled()
	 * @method bool isFeedIdChanged()
	 * @method \int remindActualFeedId()
	 * @method \int requireFeedId()
	 * @method \Avito\Export\Exchange\Setup\Model resetFeedId()
	 * @method \Avito\Export\Exchange\Setup\Model unsetFeedId()
	 * @method \int fillFeedId()
	 * @method \Avito\Export\Feed\Setup\Model getFeed()
	 * @method \Avito\Export\Feed\Setup\Model remindActualFeed()
	 * @method \Avito\Export\Feed\Setup\Model requireFeed()
	 * @method \Avito\Export\Exchange\Setup\Model setFeed(\Avito\Export\Feed\Setup\Model $object)
	 * @method \Avito\Export\Exchange\Setup\Model resetFeed()
	 * @method \Avito\Export\Exchange\Setup\Model unsetFeed()
	 * @method bool hasFeed()
	 * @method bool isFeedFilled()
	 * @method bool isFeedChanged()
	 * @method \Avito\Export\Feed\Setup\Model fillFeed()
	 * @method array getCommonSettings()
	 * @method \Avito\Export\Exchange\Setup\Model setCommonSettings(array|\Bitrix\Main\DB\SqlExpression $commonSettings)
	 * @method bool hasCommonSettings()
	 * @method bool isCommonSettingsFilled()
	 * @method bool isCommonSettingsChanged()
	 * @method array remindActualCommonSettings()
	 * @method array requireCommonSettings()
	 * @method \Avito\Export\Exchange\Setup\Model resetCommonSettings()
	 * @method \Avito\Export\Exchange\Setup\Model unsetCommonSettings()
	 * @method array fillCommonSettings()
	 * @method \boolean getUsePush()
	 * @method \Avito\Export\Exchange\Setup\Model setUsePush(\boolean|\Bitrix\Main\DB\SqlExpression $usePush)
	 * @method bool hasUsePush()
	 * @method bool isUsePushFilled()
	 * @method bool isUsePushChanged()
	 * @method \boolean remindActualUsePush()
	 * @method \boolean requireUsePush()
	 * @method \Avito\Export\Exchange\Setup\Model resetUsePush()
	 * @method \Avito\Export\Exchange\Setup\Model unsetUsePush()
	 * @method \boolean fillUsePush()
	 * @method array getPushSettings()
	 * @method \Avito\Export\Exchange\Setup\Model setPushSettings(array|\Bitrix\Main\DB\SqlExpression $pushSettings)
	 * @method bool hasPushSettings()
	 * @method bool isPushSettingsFilled()
	 * @method bool isPushSettingsChanged()
	 * @method array remindActualPushSettings()
	 * @method array requirePushSettings()
	 * @method \Avito\Export\Exchange\Setup\Model resetPushSettings()
	 * @method \Avito\Export\Exchange\Setup\Model unsetPushSettings()
	 * @method array fillPushSettings()
	 * @method \boolean getUseTrading()
	 * @method \Avito\Export\Exchange\Setup\Model setUseTrading(\boolean|\Bitrix\Main\DB\SqlExpression $useTrading)
	 * @method bool hasUseTrading()
	 * @method bool isUseTradingFilled()
	 * @method bool isUseTradingChanged()
	 * @method \boolean remindActualUseTrading()
	 * @method \boolean requireUseTrading()
	 * @method \Avito\Export\Exchange\Setup\Model resetUseTrading()
	 * @method \Avito\Export\Exchange\Setup\Model unsetUseTrading()
	 * @method \boolean fillUseTrading()
	 * @method array getTradingSettings()
	 * @method \Avito\Export\Exchange\Setup\Model setTradingSettings(array|\Bitrix\Main\DB\SqlExpression $tradingSettings)
	 * @method bool hasTradingSettings()
	 * @method bool isTradingSettingsFilled()
	 * @method bool isTradingSettingsChanged()
	 * @method array remindActualTradingSettings()
	 * @method array requireTradingSettings()
	 * @method \Avito\Export\Exchange\Setup\Model resetTradingSettings()
	 * @method \Avito\Export\Exchange\Setup\Model unsetTradingSettings()
	 * @method array fillTradingSettings()
	 * @method \boolean getUseChat()
	 * @method \Avito\Export\Exchange\Setup\Model setUseChat(\boolean|\Bitrix\Main\DB\SqlExpression $useChat)
	 * @method bool hasUseChat()
	 * @method bool isUseChatFilled()
	 * @method bool isUseChatChanged()
	 * @method \boolean remindActualUseChat()
	 * @method \boolean requireUseChat()
	 * @method \Avito\Export\Exchange\Setup\Model resetUseChat()
	 * @method \Avito\Export\Exchange\Setup\Model unsetUseChat()
	 * @method \boolean fillUseChat()
	 * @method array getChatSettings()
	 * @method \Avito\Export\Exchange\Setup\Model setChatSettings(array|\Bitrix\Main\DB\SqlExpression $chatSettings)
	 * @method bool hasChatSettings()
	 * @method bool isChatSettingsFilled()
	 * @method bool isChatSettingsChanged()
	 * @method array remindActualChatSettings()
	 * @method array requireChatSettings()
	 * @method \Avito\Export\Exchange\Setup\Model resetChatSettings()
	 * @method \Avito\Export\Exchange\Setup\Model unsetChatSettings()
	 * @method array fillChatSettings()
	 * @method \Bitrix\Main\Type\DateTime getTimestampX()
	 * @method \Avito\Export\Exchange\Setup\Model setTimestampX(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestampX)
	 * @method bool hasTimestampX()
	 * @method bool isTimestampXFilled()
	 * @method bool isTimestampXChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestampX()
	 * @method \Bitrix\Main\Type\DateTime requireTimestampX()
	 * @method \Avito\Export\Exchange\Setup\Model resetTimestampX()
	 * @method \Avito\Export\Exchange\Setup\Model unsetTimestampX()
	 * @method \Bitrix\Main\Type\DateTime fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Exchange\Setup\Model set($fieldName, $value)
	 * @method \Avito\Export\Exchange\Setup\Model reset($fieldName)
	 * @method \Avito\Export\Exchange\Setup\Model unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Exchange\Setup\Model wakeUp($data)
	 */
	class EO_Repository {
		/* @var \Avito\Export\Exchange\Setup\RepositoryTable */
		static public $dataClass = '\Avito\Export\Exchange\Setup\RepositoryTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Exchange\Setup {
	/**
	 * EO_Repository_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \string[] getNameList()
	 * @method \string[] fillName()
	 * @method \int[] getFeedIdList()
	 * @method \int[] fillFeedId()
	 * @method \Avito\Export\Feed\Setup\Model[] getFeedList()
	 * @method \Avito\Export\Exchange\Setup\EO_Repository_Collection getFeedCollection()
	 * @method \Avito\Export\Feed\Setup\EO_Repository_Collection fillFeed()
	 * @method array[] getCommonSettingsList()
	 * @method array[] fillCommonSettings()
	 * @method \boolean[] getUsePushList()
	 * @method \boolean[] fillUsePush()
	 * @method array[] getPushSettingsList()
	 * @method array[] fillPushSettings()
	 * @method \boolean[] getUseTradingList()
	 * @method \boolean[] fillUseTrading()
	 * @method array[] getTradingSettingsList()
	 * @method array[] fillTradingSettings()
	 * @method \boolean[] getUseChatList()
	 * @method \boolean[] fillUseChat()
	 * @method array[] getChatSettingsList()
	 * @method array[] fillChatSettings()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampXList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestampX()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Exchange\Setup\Model $object)
	 * @method bool has(\Avito\Export\Exchange\Setup\Model $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Exchange\Setup\Model getByPrimary($primary)
	 * @method \Avito\Export\Exchange\Setup\Model[] getAll()
	 * @method bool remove(\Avito\Export\Exchange\Setup\Model $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Exchange\Setup\EO_Repository_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Exchange\Setup\Model current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Repository_Collection merge(?EO_Repository_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Repository_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Exchange\Setup\RepositoryTable */
		static public $dataClass = '\Avito\Export\Exchange\Setup\RepositoryTable';
	}
}
namespace Avito\Export\Exchange\Setup {
	/**
	 * @method static EO_Repository_Query query()
	 * @method static EO_Repository_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Repository_Result getById($id)
	 * @method static EO_Repository_Result getList(array $parameters = [])
	 * @method static EO_Repository_Entity getEntity()
	 * @method static \Avito\Export\Exchange\Setup\Model createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Exchange\Setup\EO_Repository_Collection createCollection()
	 * @method static \Avito\Export\Exchange\Setup\Model wakeUpObject($row)
	 * @method static \Avito\Export\Exchange\Setup\EO_Repository_Collection wakeUpCollection($rows)
	 */
	class RepositoryTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Repository_Result exec()
	 * @method \Avito\Export\Exchange\Setup\Model fetchObject()
	 * @method \Avito\Export\Exchange\Setup\EO_Repository_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Repository_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Exchange\Setup\Model fetchObject()
	 * @method \Avito\Export\Exchange\Setup\EO_Repository_Collection fetchCollection()
	 */
	class EO_Repository_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Exchange\Setup\Model createObject($setDefaultValues = true)
	 * @method \Avito\Export\Exchange\Setup\EO_Repository_Collection createCollection()
	 * @method \Avito\Export\Exchange\Setup\Model wakeUpObject($row)
	 * @method \Avito\Export\Exchange\Setup\EO_Repository_Collection wakeUpCollection($rows)
	 */
	class EO_Repository_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:Avito\Export\Api\OAuth\TokenTable */
namespace Avito\Export\Api\OAuth {
	/**
	 * Token
	 * @see \Avito\Export\Api\OAuth\TokenTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string getClientId()
	 * @method \Avito\Export\Api\OAuth\Token setClientId(\string|\Bitrix\Main\DB\SqlExpression $clientId)
	 * @method bool hasClientId()
	 * @method bool isClientIdFilled()
	 * @method bool isClientIdChanged()
	 * @method \string getServiceId()
	 * @method \Avito\Export\Api\OAuth\Token setServiceId(\string|\Bitrix\Main\DB\SqlExpression $serviceId)
	 * @method bool hasServiceId()
	 * @method bool isServiceIdFilled()
	 * @method bool isServiceIdChanged()
	 * @method \string getName()
	 * @method \Avito\Export\Api\OAuth\Token setName(\string|\Bitrix\Main\DB\SqlExpression $name)
	 * @method bool hasName()
	 * @method bool isNameFilled()
	 * @method bool isNameChanged()
	 * @method \string remindActualName()
	 * @method \string requireName()
	 * @method \Avito\Export\Api\OAuth\Token resetName()
	 * @method \Avito\Export\Api\OAuth\Token unsetName()
	 * @method \string fillName()
	 * @method \string getAccessToken()
	 * @method \Avito\Export\Api\OAuth\Token setAccessToken(\string|\Bitrix\Main\DB\SqlExpression $accessToken)
	 * @method bool hasAccessToken()
	 * @method bool isAccessTokenFilled()
	 * @method bool isAccessTokenChanged()
	 * @method \string remindActualAccessToken()
	 * @method \string requireAccessToken()
	 * @method \Avito\Export\Api\OAuth\Token resetAccessToken()
	 * @method \Avito\Export\Api\OAuth\Token unsetAccessToken()
	 * @method \string fillAccessToken()
	 * @method \string getRefreshToken()
	 * @method \Avito\Export\Api\OAuth\Token setRefreshToken(\string|\Bitrix\Main\DB\SqlExpression $refreshToken)
	 * @method bool hasRefreshToken()
	 * @method bool isRefreshTokenFilled()
	 * @method bool isRefreshTokenChanged()
	 * @method \string remindActualRefreshToken()
	 * @method \string requireRefreshToken()
	 * @method \Avito\Export\Api\OAuth\Token resetRefreshToken()
	 * @method \Avito\Export\Api\OAuth\Token unsetRefreshToken()
	 * @method \string fillRefreshToken()
	 * @method \Bitrix\Main\Type\DateTime getExpires()
	 * @method \Avito\Export\Api\OAuth\Token setExpires(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $expires)
	 * @method bool hasExpires()
	 * @method bool isExpiresFilled()
	 * @method bool isExpiresChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualExpires()
	 * @method \Bitrix\Main\Type\DateTime requireExpires()
	 * @method \Avito\Export\Api\OAuth\Token resetExpires()
	 * @method \Avito\Export\Api\OAuth\Token unsetExpires()
	 * @method \Bitrix\Main\Type\DateTime fillExpires()
	 * @method \string getType()
	 * @method \Avito\Export\Api\OAuth\Token setType(\string|\Bitrix\Main\DB\SqlExpression $type)
	 * @method bool hasType()
	 * @method bool isTypeFilled()
	 * @method bool isTypeChanged()
	 * @method \string remindActualType()
	 * @method \string requireType()
	 * @method \Avito\Export\Api\OAuth\Token resetType()
	 * @method \Avito\Export\Api\OAuth\Token unsetType()
	 * @method \string fillType()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \Avito\Export\Api\OAuth\Token set($fieldName, $value)
	 * @method \Avito\Export\Api\OAuth\Token reset($fieldName)
	 * @method \Avito\Export\Api\OAuth\Token unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \Avito\Export\Api\OAuth\Token wakeUp($data)
	 */
	class EO_Token {
		/* @var \Avito\Export\Api\OAuth\TokenTable */
		static public $dataClass = '\Avito\Export\Api\OAuth\TokenTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace Avito\Export\Api\OAuth {
	/**
	 * EO_Token_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \string[] getClientIdList()
	 * @method \string[] getServiceIdList()
	 * @method \string[] getNameList()
	 * @method \string[] fillName()
	 * @method \string[] getAccessTokenList()
	 * @method \string[] fillAccessToken()
	 * @method \string[] getRefreshTokenList()
	 * @method \string[] fillRefreshToken()
	 * @method \Bitrix\Main\Type\DateTime[] getExpiresList()
	 * @method \Bitrix\Main\Type\DateTime[] fillExpires()
	 * @method \string[] getTypeList()
	 * @method \string[] fillType()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\Avito\Export\Api\OAuth\Token $object)
	 * @method bool has(\Avito\Export\Api\OAuth\Token $object)
	 * @method bool hasByPrimary($primary)
	 * @method \Avito\Export\Api\OAuth\Token getByPrimary($primary)
	 * @method \Avito\Export\Api\OAuth\Token[] getAll()
	 * @method bool remove(\Avito\Export\Api\OAuth\Token $object)
	 * @method void removeByPrimary($primary)
	 * @method void fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \Avito\Export\Api\OAuth\EO_Token_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \Avito\Export\Api\OAuth\Token current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method EO_Token_Collection merge(?EO_Token_Collection $collection)
	 * @method bool isEmpty()
	 */
	class EO_Token_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \Avito\Export\Api\OAuth\TokenTable */
		static public $dataClass = '\Avito\Export\Api\OAuth\TokenTable';
	}
}
namespace Avito\Export\Api\OAuth {
	/**
	 * @method static EO_Token_Query query()
	 * @method static EO_Token_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_Token_Result getById($id)
	 * @method static EO_Token_Result getList(array $parameters = [])
	 * @method static EO_Token_Entity getEntity()
	 * @method static \Avito\Export\Api\OAuth\Token createObject($setDefaultValues = true)
	 * @method static \Avito\Export\Api\OAuth\EO_Token_Collection createCollection()
	 * @method static \Avito\Export\Api\OAuth\Token wakeUpObject($row)
	 * @method static \Avito\Export\Api\OAuth\EO_Token_Collection wakeUpCollection($rows)
	 */
	class TokenTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_Token_Result exec()
	 * @method \Avito\Export\Api\OAuth\Token fetchObject()
	 * @method \Avito\Export\Api\OAuth\EO_Token_Collection fetchCollection()
	 *
	 * Custom methods:
	 * ---------------
	 *
	 */
	class EO_Token_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \Avito\Export\Api\OAuth\Token fetchObject()
	 * @method \Avito\Export\Api\OAuth\EO_Token_Collection fetchCollection()
	 */
	class EO_Token_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \Avito\Export\Api\OAuth\Token createObject($setDefaultValues = true)
	 * @method \Avito\Export\Api\OAuth\EO_Token_Collection createCollection()
	 * @method \Avito\Export\Api\OAuth\Token wakeUpObject($row)
	 * @method \Avito\Export\Api\OAuth\EO_Token_Collection wakeUpCollection($rows)
	 */
	class EO_Token_Entity extends \Bitrix\Main\ORM\Entity {}
}