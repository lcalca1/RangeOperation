<?php
#var_dump(string2range("1,2_3,4"));
#var_dump(range2string(array(array(), array())));
#echo range_union("1,3_5,18","5,7_8,17") . "\r\n";
#echo range_union("100,100_2,3","400,401_1,4") . "\r\n";
#echo range_union1("1,6_7,11","8,9") . "\r\n";
#echo range_intersection("3,4","1,4") . "\r\n";
echo range_left_diff("5,8_10,14_1,3","2,4_7,11_12,14") . "\r\n";
/** 
	* range_intersection
	*
	* 获得两个数值域的交集
	*
	* @param string $range1		一个值域，形如"a,b" 表示[a,b]
	* @param string $range2 	一个值域，形如"c,d" 表示[c,d]
	* @return string  	两个值域的并集 "e,f"表示[e,f] 或者 "e,f_g,h"表示[e,f]并[g,h]
	*/
	function range_intersection($range1, $range2)
	{
		if ($range1 === "" || $range2 === "") { # 空集
			return "";
		}

		#将字符串转化为数组，并取值
		$range1_temp = string2range($range1);
		$range2_temp = string2range($range2);
		$range1 = $range1_temp[0];
		$range2 = $range2_temp[0];

		$r10 = (int)$range1[0];
		$r11 = (int)$range1[1];
		$r20 = (int)$range2[0];
		$r21 = (int)$range2[1];

		if ( ($r11 < $r20) || ($r21 < $r10) ) { # 无交集
			$result = array(array());
		} 
		else if ( ($r10 < $r20) && ($r20 < $r11) && ($r11 < $r21) ) {# [1,3] [2,4]
			$result = array(array($r20, $r11), );
		} 
		else if ( ($r20 < $r10) && ($r10 < $r21) && ($r21 < $r11) ) {# [2,4] [1,3]
			$result = array(array($r10, $r21), );
		}
		else if ( ( ($r10 < $r20) || ($r10 == $r20) ) && ( ($r21 < $r11) || ($r21 == $r11) ) ) {#包含 [1,4] [2,3]
			$result = array($range2);
		}
		else if ( ( ($r20 < $r10) || ($r20 == $r10) ) && ( ($r11 < $r21) || ($r11 == $r21) ) ){#包含 [2,3] [1,4]
			$result = array($range1);
        }
        else if ( ($r10 == $r20) && ($r11 == $r21) ) {# 相等
            $result = array($range1);
        } 
        else if ( $r11 == $r20 ) {# [1,2] [2,3]
            $result = array(array($r11, $r11));
        } 
        else if ( $r21 == $r10 ) {# [2,3] [1,2]
            $result = array(array($r10, $r10));
        }

        # 返回字符串
		return range2string($result);
	}
/** 
	* range_union
	*
	* 获得两个数值域的并集
	*
	* @param string $range1		一个值域，形如"a1,b1_a2,b2_..." 并且 互不相交
	* @param string $range2 	一个值域，形如"c1,d1_c2,d2_..." 并且 互不相交
	* @return string  	两个值域的并集 "e,f"表示[e,f] 或者 "e,f_g,h"表示[e,f]并[g,h]
	*/
	function range_union($range1, $range2)
	{
		$range2 = explode("_", $range2);
		return array_reduce($range2, "range_union1", $range1);
	}
/** 
	* range_union1
	*
	* 获得两个数值域的并集
	*
	* @param string $range1		一个值域，形如"a1,b1_a2,b2_..." 并且 互不相交
	* @param string $range2 	一个值域，形如"c,d" 表示[c,d]
	* @return string  	两个值域的并集 "e,f"表示[e,f] 或者 "e,f_g,h"表示[e,f]并[g,h]
	*/
	function range_union1($range1, $range2)
	{
		# 包含空集的情况
		if ($range1 === "") {
			return $range2;
		}
		if ($range2 === "") {
			return $range1;
		}

		$range1 = explode("_", $range1);
		$has_intersection = false; # 标识是否具有交集
		$i = 0;# 记录当前元素的位置

		foreach ($range1 as $range1_each) { #对range1遍历，查看是否和range2有交集，以此判断是否需要做并集
			# 若有交集，则用 range1_each 和 range2 的并集替代
			if (range_intersection($range1_each, $range2) !== "") {
				$has_intersection = true;
				$range1[$i] = range_union2($range1_each, $range2);
				break;
			}
			$i ++;
		}

		if ($has_intersection) {# 若之前做过并集， 则对新得到的集合 挨个 求并集
			return array_reduce($range1, "range_union1", "");
		} else {# 否则，将range2放入range1的最后，并返回
			$range1[] = $range2;
			return implode("_", $range1);
		}
	}
/** 
	* range_union2
	*
	* 获得两个数值域的并集
	*
	* @param string $range1		一个值域，形如"a,b" 表示[a,b]
	* @param string $range2 	一个值域，形如"c,d" 表示[c,d]
	* @return string  	两个值域的并集 "e,f"表示[e,f] 或者 "e,f_g,h"表示[e,f]并[g,h]
	*/
	function range_union2($range1, $range2)
	{
		# 包含空集的情况
		if ($range1 === "") {
			return $range2;
		}

		if ($range2 === "") {
			return $range1;
		}

		#将字符串转化为数组，并取值
		$range1_temp = string2range($range1);
		$range2_temp = string2range($range2);
		$range1 = $range1_temp[0];
		$range2 = $range2_temp[0];

		$r10 = (int)$range1[0];
		$r11 = (int)$range1[1];
		$r20 = (int)$range2[0];
		$r21 = (int)$range2[1];

		if ( ($r11 < $r20) || ($r21 < $r10) ) { # 无交集
			$result[] = $range1;
			$result[] = $range2;
		} 
		else if ( ($r10 < $r20) && ($r20 < $r11) && ($r11 < $r21) ) {# [1,3] [2,4]
			$result[] = array($r10, $r21);
		} 
		else if ( ($r20 < $r10) && ($r10 < $r21) && ($r21 < $r11) ) {# [2,4] [1,3]
			$result[] = array($r20, $r11);
		}
		else if ( ( ($r10 < $r20) || ($r10 == $r20) ) && ( ($r21 < $r11) || ($r21 == $r11) ) ) {#包含 [1,4] [2,3]
			$result[] = $range1;
		}
		else if ( ( ($r20 < $r10) || ($r20 == $r10) ) && ( ($r11 < $r21) || ($r11 == $r21) ) ){#包含 [2,3] [1,4]
			$result[] = $range2;
        }
        else if ( ($r10 == $r20) && ($r11 == $r21) ) {#相等
            $result[] = $range1;
        } 
        else if ( $r11 == $r20 ) {# [1,2] [2,3]
            $result[] = array($r10, $r21);
        } 
        else if ( $r21 == $r10 ) {# [2,3] [1,2]
            $result[] = array($r20, $r11);
        }

		return range2string($result);
	}
	/** 
	* range_left_diff
	*
	* 获得 在$range1中 而不在 $range2中 的部分，如 range_left_diff("1,3", "2,4") 返回 "1,2"
	* 因为这里不支持 集合的 开闭，所以单独的点 range_left_diff("5,8","7,7") 会返回 ("5,7","7,8")这种结果（我们认为都是闭区间）  
	*
	* @param string $range1		一个值域，形如"a1,b1_a2,b2_..." 并且 互不相交
	* @param string $range2 	一个值域，形如"c1,d1_c2,d2_..." 并且 互不相交
	* @return string  	在$range1 而不在 $range2的部分 "e1,f1_e2,f2_..."
	*/
	function range_left_diff($range1, $range2)
	{
		$range2 = explode("_", $range2);
		return array_reduce($range2, "range_left_diff1", $range1);
	}

/** 
	* range_left_diff1
	*
	* 获得 在$range1 而不在 $range2 的部分
	*
	* @param string $range1		一个值域，形如"a1,b1_a2,b2_..." 并且 互不相交
	* @param string $range2 	一个值域，形如"c,d" 表示[c,d]
	* @return string  	在$range1 而不在 $range2的部分 "e1,f1_e2,f2_..."
	*/
	function range_left_diff1($range1, $range2)
	{
		# 包含空集的情况
		if ($range1 === "") {
			return "";
		}

		if ($range2 === "") {
			return $range1;
		}
		
		#将字符串转化为数组，并取值
		$range1_temp = string2range($range1);
		$range2_temp = string2range($range2);
		$range2 = $range2_temp[0];
		$r20 = (int)$range2[0];
		$r21 = (int)$range2[1];

		$result = array();

		foreach ($range1_temp as $range1) {

			$r10 = (int)$range1[0];
			$r11 = (int)$range1[1];
		

			if ( ($r11 < $r20) || ($r21 < $r10) ) { # 无交集
				$result[] = $range1;
			} 
			else if ( ($r10 < $r20) && ($r20 < $r11) && ($r11 < $r21) ) {# [1,3] [2,4]
				$result[] = array($r10, $r20);
			} 
			else if ( ($r20 < $r10) && ($r10 < $r21) && ($r21 < $r11) ) {# [2,4] [1,3]
				$result[] = array($r21, $r11);
			}
			else if ( ($r10 < $r20) && ($r21 < $r11) ) {#完全包含 [1,4] [2,3]
				$result[] = array($r10, $r20);
				$result[] = array($r21, $r11);
			}
			else if ( ($r10 < $r20) && ($r21 == $r11) ) {#包含且右边界相等 [1,3], [2,3]
				$result[] = array($r10, $r20);
			}
			else if ( ($r10 == $r20) && ($r21 < $r11) ) {#包含且左边界相等 [1,3], [1,2]
				$result[] = array($r21, $r11);
			}
			else if ( ( ($r20 < $r10) || ($r20 == $r10) ) && ( ($r11 < $r21) || ($r11 == $r21) ) ){#包含 [2,3] [1,4]
        	}
        	else if ( $r11 == $r20 ) {# [1,2] [2,3]
            	$result[] = $range1;
        	} 
        	else if ( $r21 == $r10 ) {# [2,3] [1,2]
            	$result[] = $range1;
        	}
    	}

		return range2string($result);
	}

/** 
	* string2range
	*
	* 将字符串形式的域转化为数组形式的,(数组中为string类型的数字)
	*
	* @param string $range_string	一个值域，形如"a,b" 表示[a,b]; "a,b_c,d"表示[a,b]并[c,d]
	* @return array(array(a,b),array(c,d),...) ，空字符串将会返回 array(array())即，包含一个空数组的数组
	*/
	function string2range($range_string)
	{
        if ($range_string==="") {
            return array(array());
        }

		$array_temp = explode("_", $range_string);

		$explode_comma = function($r){
			return explode(",", $r);
		};

		return array_map($explode_comma, $array_temp);
	}
/** 
	* range2string
	*
	* 将数组形式的域转化为字符串形式的
	*
	* @param array	包含若干域的数组，空的域用空数组表示
	* @return 一个值域的字符串形式，形如"a,b" 表示[a,b]; "a,b_c,d"表示[a,b]并[c,d]
	*/
	function range2string($range_array)
	{
		if ( $range_array[0] === array() ) {
			return "";
		}

		$implode_comma = function($r) {
			return implode(",", $r);
		};

		$array_temp = array_map($implode_comma, $range_array);

		return implode("_", $array_temp);
	}
?>
