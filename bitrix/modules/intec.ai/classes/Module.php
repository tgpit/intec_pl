<?php
namespace intec\ai;

use Bitrix\Main\Config\Configuration;
use intec\core\helpers\Json;
use Bitrix\Main\Text\Encoding;

class Module {
	private array $headers;

	public function __construct($secretKey) {
		$this->headers = [
			"Content-Type: application/json",
			"Authorization: Bearer ".$secretKey,
		];
	}

	public function sendCurl($url, $data) {
		$data = Json::encode($data, JSON_HEX_APOS, true);

		$curl_info = [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => $this->headers,
		];

		$proxy = \COption::GetOptionString("intec.ai", "ai.proxy");
		$proxyUse = \COption::GetOptionString("intec.ai", "ai.proxyUse") == '1';
	
		$successResponse = null;
	
		if (!empty($proxy) && $proxyUse) {
			$proxyArray = explode("\n", $proxy);
	
			foreach ($proxyArray as $proxyLine) {
				\COption::SetOptionString("intec.ai", "ai.currentProxy", trim($proxyLine));
				$curl_info[CURLOPT_PROXY] = \COption::GetOptionString("intec.ai", "ai.currentProxy");
	
				$curl = curl_init();
	
				curl_setopt_array($curl, $curl_info);
				$response = curl_exec($curl);
	
				if (!($response === false) && !empty($response)) {
					$successResponse = $response;
					break;
				}
			}
		}
	
		if ($successResponse === null) {
			unset($curl_info[CURLOPT_PROXY]);

			$curl = curl_init();
	
			curl_setopt_array($curl, $curl_info);
			$response = curl_exec($curl);
		} else {
			$response = $successResponse;
		}
	
		curl_close($curl);
	
		$response = json_decode($response);
	
		return $response;
	}

	public function generateText($prompt) {
		$maxTokens = \COption::GetOptionString("intec.ai", "ai.maxTokens");
		
		$data = [
			"model" => "gpt-3.5-turbo-instruct",
			"prompt" => $prompt,
			"max_tokens" => (($maxTokens < 0) || empty($maxTokens) || !is_numeric($agentInterval)) ? 2000 : intval($maxTokens)
		];
		
		$response = $this->sendCurl('https://api.openai.com/v1/completions', $data);

		$result = [];

		if (!empty($response)) {
			if (!empty($response->choices[0]->text)) {
				$result['TEXT'] = $response->choices[0]->text;
				$result['STATUS'] = 'success';
			} else if (!empty($response->error)) {
				$result['TEXT'] = $response->error->message;
				$result['STATUS'] = 'error';
			}
		} else {
			$result['TEXT'] = "- No response -";
			$result['STATUS'] = 'error';
		}
		
		return $result;
	}

	public static function processaiTask() {
		$db = \Bitrix\Main\Application::getInstance()->getConnection();
		$query = "SELECT * FROM ai_tasks WHERE done IS NULL OR done != 'Y' LIMIT 1";
    	$result = $db->query($query);

		$secret = \COption::GetOptionString("intec.ai", "ai.secret");

		if ($task = $result->fetch()) {
			$module = new self($secret);
			
			$prompt = $task['prompt'];
			
			$generated = $module->generateText($prompt);
			$generatedText = $generated['TEXT'];

			if (Encoding::detectUtf8($generatedText))
				$generatedText = Encoding::convertEncoding($generatedText, 'UTF-8', LANG_CHARSET);

			$generatedText = $db->getSqlHelper()->forSql($generatedText);

			$taskId = intval($task['id']);

			$updateQuery = '';

			if ($generated['STATUS'] == 'success') {
				$updateQuery = "UPDATE ai_tasks SET done = 'Y', error = '', generationResult = '$generatedText' WHERE id = $taskId";
			} else if ($generated['STATUS'] == 'error') {
				$updateQuery = "UPDATE ai_tasks SET done = 'Y', error = '$generatedText' WHERE id = $taskId";
			}

			$db->query($updateQuery);			
		}
	}

	public static function generateFromQuene() {
		try {
			self::processaiTask();
		} catch (\Exception $e) {
			print_r($e->getMessage());
		}

		return "\\intec\\ai\\Module::generateFromQuene();";
	}
}