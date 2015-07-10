<?php

/**
 * PaizaTester
 */
class PaizaTester {

	/**
	 * 実行コードのファイルフォーマット
	 */
	const CODE_FILE_FORMAT = 'code/%s%s.php';

	/**
	 * テストケースディレクトリのフォーマット
	 */
	const CASE_DIR_FORMAT = 'case/%s/';

	/**
	 * テストケースファイルの拡張子
	 */
	const TESTCASE_EXTENSION = '_test.txt';

	/**
	 * 結果ファイルの拡張子
	 */
	const RESULT_EXTENSION = '_result.txt';

	/**
	 * コードを実行する際のコマンド
	 * 実際に走らせる php は /usr/local/bin/php を想定
	 */
	const COMMAND_FORMAT = 'php %s <%s';

	/**
	 * コード
	 * @var string Z999など
	 */
	private $_code = null;

	/**
	 * テストケースファイルのリスト
	 * @var array
	 */
	private $_testcase = array();

	/**
	 * コンストラクタ
	 * @param string $code
	 */
	public function __construct($code) {
		$this->setCode($code);
		$this->run();
	}

	/**
	 * コードを取得
	 * @return string
	 */
	public function getCode() {
		return $this->_code;
	}

	/**
	 * コードファイルを取得
	 * @return string
	 */
	public function getCodeFile($suffix = null) {
		if (!is_null($suffix)) {
			$suffix = '_' . $suffix;
		}
		return sprintf(self::CODE_FILE_FORMAT, $this->getCode(), $suffix);
	}

	/**
	 * 全てのコードファイルを取得
	 * @return array
	 */
	public function getAllCodeFile() {
		return glob(sprintf(self::CODE_FILE_FORMAT, $this->getCode(), '*'));
	}

	/**
	 * テストケースディレクトリを取得
	 * @return string
	 */
	public function getCaseDir() {
		return sprintf(self::CASE_DIR_FORMAT, $this->getCode());
	}

	/**
	 * テストケースファイルのリストを取得
	 * @return string
	 */
	public function getTestCase() {
		if (empty($this->_testcase)) {
			$files = glob($this->getCaseDir() . '*.txt');
			if (empty($files)) {
				return $this->_testcase;
			}
			foreach ($files as $file) {
				$key = null;
				if (strpos($file, self::TESTCASE_EXTENSION) !== false) {
					$key = 'test';
				}
				else if (strpos($file, self::RESULT_EXTENSION) !== false) {
					$key = 'result';
				}
				else {
					continue;
				}
				list($number, ) = explode('_', $file);
				if (empty($this->_testcase[$number])) {
					$this->_testcase[$number] = array(
						'test' => false,
						'result' => false
					);
				}
				$this->_testcase[$number][$key] = $file;
			}
		}
		return $this->_testcase;
	}

	/**
	 * テストを実行
	 */
	public function run() {
		echo str_repeat('-', 50) . PHP_EOL;
		printf('PaizaTester' . PHP_EOL);
		$codes = $this->getAllCodeFile();
		foreach ($this->getTestCase() as $file) {
			foreach ($codes as $code) {
				$suffix = null;
				if (preg_match('/' . $this->getCode() . '_(.*)\.php/', $code, $match)) {
					$suffix = $match['1'];
				}
				$codefile = $this->getCodeFile($suffix);
				if (!file_exists($codefile)) {
					// コードファイルが存在しない場合は次へ
					continue;
				}
				$cmd = sprintf(self::COMMAND_FORMAT, $codefile, $file['test']);
				$result = shell_exec($cmd);
				$answer = false;
				if (!empty($file['result']) && file_exists($file['result'])) {
					$answer = file_get_contents($file['result']);
				}
				echo str_repeat('-', 50) . PHP_EOL;
				echo $cmd . PHP_EOL;
				echo $result . PHP_EOL;
				if ($answer && trim($result) === trim($answer)) {
					echo '... Success' . PHP_EOL;
				} else {
					echo '... Failed' . PHP_EOL;
					echo 'Answer = ' . $answer . PHP_EOL;
				}

				echo PHP_EOL;
			}
		}
	}

	/**
	 * コードを設定
	 * @param string $code
	 * @throws Exception
	 */
	private function setCode($code) {
		if (!preg_match('/^[A-Z]\d{3}$/', $code)) {
			throw new Exception('正しいコードを入力してください。入力したコード=' . $code);
		}
		$this->_code = $code;
		$this->_testcase = array();
		if (!file_exists($this->getCodeFile()) && count($this->getAllCodeFile()) === 0) {
			throw new Exception('コードファイルが存在しません。');
		}
		if (!is_dir($this->getCaseDir())) {
			throw new Exception('テストケースフォルダが存在しません。');
		}
		if (!$this->getTestCase()) {
			throw new Exception('テストケースが存在しません。');
		}
	}

}
