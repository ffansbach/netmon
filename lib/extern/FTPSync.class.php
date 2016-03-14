<?php
/**
 * @access public
 * @author amin saeedi, <amin.w3dev@gmail.com>
 * @author keyhan sedaghat<keyhansedaghat@netscape.net>
 * @version 1.1
 */
class FTPSync{
	private $ftpServer;
	private $ftpUserName;
	private $ftpPassWord;
	private $ftpPort;
	private $resource = null;
	private $localFiles = array();
	private $ignoreList = array();
	private $allowDownload = false;
	private $log = false;
	private $logFile;
	
	 /**
     * CONSTRUCTOR
     *
     * @access public
     * @param  string server: ftp server name
     * @param  string user: ftp username
     * @param  string pass: ftp password
     * @param  string port: ftp port
     */
	public function __construct($server, $user, $pass, $port = 21){
		$this->ftpServer = $server;
		$this->ftpUserName = $user;
		$this->ftpPassWord = $pass;
		$this->ftpPort = $port;
	}
	
	 /**
     * connect to ftp server
     *
     * @access public
     * @param  int timeoout: timeout for ftp connection
     * @return boolean
     */
	public function connect($timeout = 90){
			$ftp = ftp_connect($this->ftpServer, $this->ftpPort, $timeout);	//connect to ftp server
			if(!$ftp){
				echo "Unable to connect to ftp server";
				return false;
			}else{
				$this->resource = $ftp;
			}	
			//if connection is made successfully,do login
			$login = ftp_login($this->resource, $this->ftpUserName, $this->ftpPassWord); 
		
			if(!$login){
				echo "Could not connect as {$this->ftpUserName}";
				return false;
			}
			return true;
	}
	
	public function saveLog($logFile="syncFtp.log"){
		$this->logFile = $logFile;
		$this->log = true;
	}
	
	 /**
     * Set passive mode
     *
     * @access public
     * @param  boolean value
     * @return boolean
     */
	public function setPassive($value){
		if(is_null($this->resource)){
			$this->connect();
		}
		$result = ftp_pasv($this->resource, (boolean)$value);
		if(!$result){
			echo "<p>Passive mode can not be enabled</p>";
		}
		return $result;
	}
	
	/**
     * Get all remote file 
     *
     * @access public
     * @param  string dir: directory for get its files
     * @return array
     */
	public function getAllRemFiles($dir="."){
		if(is_null($this->resource)){
			$this->connect();
		}
		 return ftp_nlist($this->resource, $dir);
	}
	
	/**
     * Get present working directory
     *
     * @access public
     * @return string: current directory
     */
	public function pwd(){
		if(is_null($this->resource)){
			$this->connect();
		}
		return ftp_pwd($this->resource);
	}
	
	/**
     * Change current directory
     *
     * @access public
     * @return string: current directory
     */
	public function chdir($dirname){
		if(is_null($this->resource)){
			$this->connect();
		}
		return ftp_chdir($this->resource, $dirname);
	}
	
	/**
     * Set ignore item: directories that should not be traversed
     *
     * @access public
     * @param string value
     */
	public function setFilter($value){
		$this->ignoreList[] = $value;
	}
	
	/**
     * Allow to download if remote file is newer
     *
     * @access public
     */
	public function allowDownload(){
		$this->allowDownload = true;
	}
	
	/**
     * travere given directory
     *
     * @access private
     * @param string dir
     */
	private function localRecursive($dir){
		$handle = new DirectoryIterator($dir);
		foreach($handle as $f){
			if(!$f->isDot() && !in_array($f, $this->ignoreList)){
				if($f->isDir()){
					self::localRecursive($dir."/".$f);
				}elseif($f->isFile()){
					$this->localFiles[] = $dir."/".strval($f);
				}
			}
		}
		return;
	}
	
	/**
     * synchronize local directory with remote directory
     *
     * @access private
     * @param string srcDir
     * @param string dstDir
     * @param int timeOffset: acceptable threshold for file modified time difference(seconds)
     */
	public function sync($srcDir, $dstDir, $timeOffset=600){
		ob_start();
		
		if(!is_dir($srcDir)){
			echo "Directory address is invalid: $srcDir";
			return false;
		}
		echo "Synchronizing started at ".date("Y-m-d H:i:s")."\n";
		@ftp_mkdir($this->resource, $dstDir);
		$this->localRecursive($srcDir);
		if(count($this->localFiles) > 0){
			foreach($this->localFiles as $file){
				$remotePath = str_ireplace($srcDir, $dstDir, $file);		
				$remotePath = str_replace("\\", "/", $remotePath);
				$remMtime = ftp_mdtm($this->resource,$remotePath);
				$locMTime = filemtime($file);
				if( $remMtime < $locMTime-$timeOffset ){
					if(ftp_put($this->resource, $remotePath, $file, FTP_BINARY)){
						echo "$file is successfully uploaded to $remotePath \n";
					} else {
						echo "Error in Uploading $file\n";
					}
				}elseif( $this->allowDownload && $remMtime > $locMTime+$timeOffset ){
					if(ftp_get($this->resource, $file, $remotePath, FTP_BINARY)){
						echo "$remotePath is successfully downloaded to $file \n";
					}
				}
			}
			echo "Synchronization is finished at ".date("Y-m-d H:i:s")."\n";
		}
		$data = ob_get_clean();
		if($this->log){
			$header = "Connected {$this->ftpUserName}@{$this->ftpServer}.\n";
			$header .= "Ignored directories/files are ".print_r($this->ignoreList,true).". \n";
			$header .= "Time offset is $timeOffset seconds.\n";
			$footer = str_repeat("-",100)."\n";
			file_put_contents($this->logFile, $header.$data.$footer, FILE_APPEND);
		}
		echo nl2br($data);
	}
}
?>