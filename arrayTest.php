<?php
/**
 * 第一题、截取最后一个/之前的字符，相同的后两个元素相加
 */
$items = array(
	array('http://www.abc.com/a/', 100, 120),
	array('http://www.abc.com/b/index.php', 50, 80),
	array('http://www.abc.com/a/index.html', 90, 100),
	array('http://www.abc.com/a/?id=12345', 200, 33),
	array('http://www.abc.com/c/index.html', 10, 20),
	array('http://www.abc.com/abc/', 10, 30)
);
$result = [];
foreach ($items as $val) {
	$str_pos = strrpos($val[0], '/');
	$result_key = substr($val[0], 0,$str_pos+1);
	if (isset($result[$result_key])) {
		$result[$result_key][1] += $val[1];
		$result[$result_key][2] += $val[2];
	}else{
		$result[$result_key] = [$result_key,$val[1],$val[2]];
	}
}
$result = array_values($result);
var_dump($result);


/**
 * 第二题
 * [king 猴子选大王]
 * @param  int $n [猴子总数]
 * @param  int $m [第几位踢出]
 */
function king($n, $m){
	if ($n <2) {
		return $n;
	}
	$monkey_arr = range(1, $n);
	$i = 0;
	while (count($monkey_arr)>1) {
		$number = array_shift($monkey_arr);
		if (($i+1)%$m != 0) {
			array_push($monkey_arr, $number);
		}
		$i++;
	}
	return $monkey_arr[0];
}

$king = king(3,3);
echo $king;die;


/**
 * 第三题，每题得分是5分，那么这个同学得分是多少？
 */
// 得分计算，已知道选题提交的答案是
$commits = 'A,B,B,A,C,C,D,A,B,C,D,C,C,C,D,A,B,C,D,A';
// 实际的答案是：
$answers = 'A,A,B,A,D,C,D,A,A,C,C,D,C,D,A,B,C,D,C,D';

$commits_arr = explode(',', $commits);
$answers_arr = explode(',', $answers);
//按索引取两数组差集
$result = array_intersect_assoc($commits_arr,$answers_arr);
$point  = count($result) * 5;
echo "得分为：" . $point,PHP_EOL;


/**
 * 第四道、使用php://input接收参数存入文件后，读取缓存
 */
$post_data = file_get_contents('php://input');
if (!isset($post_data['uid']) || empty($post_data['uid'])) {
	echo "参数错误",PHP_EOL;exit;
}
$cache_path	= 'user.php';
if (!file_exists($cache_path)) {
	$post_data['uid'] = 1;
	//连接数据库
	$connect = mysqli_connect('127.0.0.1','root','root','test','3306');
	if (!$connect) {
	    exit("could not connect to the database:\n" . mysql_error());//诊断连接错误
	}
	//查找数据
	$sql = 'SELECT * FROM user WHERE uid="'.(int)$post_data['uid'].'"';
	$res = $connect->query($sql);
	if ($res->num_rows <= 0) {
		exit;
	}
	$user_data = [];
	while ($row = $res->fetch_assoc()) {
	    $user_data[] = $row;
	}
	$connect->close();
	//存入文件缓存
	$user_db = '<?php return ';
	$user_db .= var_export($user_data,true);
	$user_db .= ';';
	file_put_contents($cache_path, $user_db);
}else{
	//获取数组
	$new_user_db = include ($cache_path);
	var_dump($new_user_db);
}



/**
 * 第五题、实现一个对象的数组式访问接口
 */
class obj implements arrayaccess {
    private $container = array();
    public function __construct() {
        $this->container = array(
            "one"   => 1,
            "two"   => 2,
            "three" => 3,
        );
    }
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}

$obj = new obj;

var_dump(isset($obj["two"]));
var_dump($obj["two"]);
unset($obj["two"]);
var_dump(isset($obj["two"]));
$obj["two"] = "A value";
var_dump($obj["two"]);
$obj[] = 'Append 1';
$obj[] = 'Append 2';
$obj[] = 'Append 3';
print_r($obj);


/**
 * 第六题1000瓶水只有一瓶水有毒，最少需要几只小白鼠
 */
// 1111101000是1000的二进制，所以需要10个小白鼠，每个小白鼠代表其中一位，编号1-10，1喝，0不喝，最后看哪几只死了，然后转为10进制
// 例如是第一号水，2进制为0000000001，这时候只有10号小白鼠喝了这瓶水，其他没有喝，如果只有10号小白鼠死了，证明一号有毒
// 因为只有一瓶水有毒，所以小白鼠死亡的编号结果是唯一的，10只小白鼠最多可以判断1024瓶水是否有毒

/**
 * 第七题、使用serialize序列化一个对象，并使用__sleep和__wakeup方法。
 */

class Bag{
    public $money;
    function __construct($string){
        $this->money = $string;
    }
    public function __sleep(){
        $this->money = '不能让你知道我有多少钱';
        return array('money');

    }
    public function __wakeup(){
        $this->money = '还是不能让你知道我有多少钱';
    }
}
$yourBag = new Bag('你有多少钱');
print_r(serialize($yourBag));
print_r(unserialize(serialize($yourBag)));

/**
 * 第八题、利用数组栈实现翻转字符串功能
 */
$str = '1234567890';
$arr = [];
for ($i=0; $i < strlen($str); $i++) { 
	array_push($arr, $str[$i]);
}
$result = '';
while (!empty($arr)) {
	$result .= array_pop($arr);
}
echo $result;


/**
 * 第九题、11, 18, 12, 1, -2, 20, 8, 10, 7, 6中取出所有和为18的组合
 */
$k           = 18;
$array       = [11, 18, 12, 1, -2, 20, 8, 10, 7, 6];
$count       = count($array);
$group_count = 2<<9;
//10个数做组合会出现1024种情况
for ($i=1; $i <= $group_count; $i++) { 
	$item     = decbin($i);
	$item     = sprintf('%010s', $item);
	$item_arr = [];
	for ($j=0; $j < $count; $j++) { 
		if ($item[$j] == 1) {
			$item_arr[] = $array[$j];
		}
	}
	if (array_sum($item_arr) == $k) {
		echo implode(',', $item_arr),PHP_EOL;
	}
}
