<?php

/**
 * PaizaTester
 */
class PaizaTester {

	/**
	 * 実行コードのファイルフォーマット
	 */
	const CODE_FILE_FORMAT = 'code/%s.php';

	/**
	 * テストケースディレクトリのフォーマット
	 */
	const CASE_DIR_FORMAT = 'case/%s/';

	/**
	 * テストケースファイルの拡張子
	 */
	const TESTCASE_EXTENSION = 'txt';

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
	public function getCodeFile() {
		return sprintf(self::CODE_FILE_FORMAT, $this->getCode());
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
			$this->_testcase = glob($this->getCaseDir() . '*.' . self::TESTCASE_EXTENSION);
		}
		return $this->_testcase;
	}

	/**
	 * テストを実行
	 */
	public function run() {
		echo str_repeat('-', 50) . PHP_EOL;
		printf('PaizaTester' . PHP_EOL);
		foreach ($this->getTestCase() as $testcase_file) {
			$cmd = sprintf(self::COMMAND_FORMAT, $this->getCodeFile(), $testcase_file);
			$result = shell_exec($cmd);
			echo str_repeat('-', 50) . PHP_EOL;
			printf('%s' . PHP_EOL . '%s' . PHP_EOL . PHP_EOL, $cmd, $result);
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
		if (!file_exists($this->getCodeFile())) {
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
