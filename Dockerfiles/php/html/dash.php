<?PHP

define('DB_HOST', 'dash-db');
define('DB_PORT', '3306');
define('DB_NAME', 'dash');
define('DB_USERNAME', 'dash');
define('DB_PASSWORD', 'dash123');

class Dash {

	private $connection;
	
	function __construct() {
        $this->connection= new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        $this->connection->set_charset("utf8");
        date_default_timezone_set("Europe/Stockholm");
    }

	function getDBConnection() {
		return $this->connection;
	}

	function getAppId($app) {
    	$sql= "SELECT id FROM apps WHERE name='$app'";
        $results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) {
			$qry= "INSERT INTO apps (name) VALUES ('$app')";
			$results= mysqli_query( $this->connection, $qry );
			return mysqli_insert_id($this->connection);
		}
		$row = mysqli_fetch_assoc($results);
		return $row['id'];
	}

	function getEnvGroupId($env) {
    	$sql= "SELECT id FROM envGroups WHERE name='$env'";
        $results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) {
			$qry= "INSERT INTO envGroups (name) VALUES ('$env')";
			$results= mysqli_query( $this->connection, $qry );
			return mysqli_insert_id($this->connection);
		}
		$row = mysqli_fetch_assoc($results);
		return $row['id'];
	}

	function getEnvId($envGroupId, $env) {
    	$sql= "SELECT id FROM envs WHERE envGroup=$envGroupId AND name='$env'";
        $results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) {
			$qry= "INSERT INTO envs (envGroup, name) VALUES ($envGroupId, '$env')";
			$results= mysqli_query( $this->connection, $qry );
			return mysqli_insert_id($this->connection);
		}
		$row = mysqli_fetch_assoc($results);
		return $row['id'];
	}

	function updateVersion($app, $envGroup, $env, $ver, $date, $log) {
		$appId= $this->getAppId($app);
		$envGroupId= $this->getEnvGroupId($envGroup);
		$envId= $this->getEnvId($envGroupId, $env);
		$localDate= date("Y-m-d H:i:s", $date);
		$qry= "INSERT INTO deployments (app, env, `version`, `date`, `log`) VALUES ($appId, $envId, '$ver', '$localDate', '$log')";
		$result= mysqli_query( $this->connection, $qry );
	}

	function getApps() {
    	$sql= "SELECT * FROM apps";
		$data= array();
		$results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) return $data;
		while ($row= mysqli_fetch_assoc($results)) {
			$data[]= $row;
		}
		return $data;
	}
/*
	function getEnvGroups() {
    	$sql= "SELECT * FROM envGroups";
		$data= array();
		$results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) return $data;
		while ($row= mysqli_fetch_assoc($results)) {
			$data[]= $row;
		}
		return $data;
	}

	function getEnvGroups($envs) {
		$envList= implode(',', $envs);
    	$sql= "SELECT id, envGroup FROM `envs` WHERE id IN ($envList) GROUP BY envGroup";
		$data= array();
		$results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) return $data;
		while ($row= mysqli_fetch_assoc($results)) {
			$data[]= $row;
		}
		return $data;
	}

	function getEnvs($deployments) {
		$data= array();
		foreach($deployments as $deployment) {
		    if(!in_array($deployment['env'], $data, true)) {
		        array_push($data, $deployment['env']);
		    }
		}
		return $data;
	}
*/
	function getEnvNames() {
    	$sql= "SELECT * FROM envs";
		$data= array();
		$results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) return $data;
		while ($row= mysqli_fetch_assoc($results)) {
			$data[$row['id']]= $row['name'];
		}
		return $data;
	}

	function getEnvGroupNames() {
    	$sql= "SELECT * FROM envGroups";
		$data= array();
		$results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) return $data;
		while ($row= mysqli_fetch_assoc($results)) {
			$data[$row['id']]= $row['name'];
		}
		return $data;
	}

	function getEnvGroups($deployments) {
		$data= array();
		foreach($deployments as $deployment) {
		    if(!in_array($deployment['envGroup'], $data, true)) {
		        array_push($data, $deployment['envGroup']);
		    }
		}
		return $data;
	}

	function getDeployments($app) {
		$sql="SELECT d1.env as env, d1.version as version, d1.date as date, d1.log as log, e.envGroup as envGroup FROM deployments d1, envs e WHERE e.id=d1.env AND d1.app=$app AND date=(SELECT MAX(d2.date) FROM deployments d2 WHERE d1.app=d2.app AND d1.env=d2.env)";
		$data= array();
		$results= mysqli_query ($this->connection, $sql);
		if(!$results || mysqli_num_rows($results) <= 0) return $data;
		while ($row= mysqli_fetch_assoc($results)) {
			$data[]= $row;
		}
		return $data;
	}
}

?>