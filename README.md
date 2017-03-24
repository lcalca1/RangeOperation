# RangeOperation
union, difference、intersection of  range

* range_intersection
	*
	* 获得两个数值域的交集
	*
	* @param string $range1		一个值域，形如"a,b" 表示[a,b]
	* @param string $range2 	一个值域，形如"c,d" 表示[c,d]
	* @return string  	两个值域的并集 "e,f"表示[e,f] 或者 "e,f_g,h"表示[e,f]并[g,h]

* range_union
	*
	* 获得两个数值域的并集
	*
	* @param string $range1		一个值域，形如"a1,b1_a2,b2_..." 并且 互不相交
	* @param string $range2 	一个值域，形如"c1,d1_c2,d2_..." 并且 互不相交
	* @return string  	两个值域的并集 "e,f"表示[e,f] 或者 "e,f_g,h"表示[e,f]并[g,h]

* range_left_diff
	*
	* 获得 在$range1中 而不在 $range2中 的部分，如 range_left_diff("1,3", "2,4") 返回 "1,2"
	* 因为这里不支持 集合的 开闭，所以单独的点 range_left_diff("5,8","7,7") 会返回 ("5,7","7,8")这种结果（我们认为都是闭区间）  
	*
	* @param string $range1		一个值域，形如"a1,b1_a2,b2_..." 并且 互不相交
	* @param string $range2 	一个值域，形如"c1,d1_c2,d2_..." 并且 互不相交
	* @return string  	在$range1 而不在 $range2的部分 "e1,f1_e2,f2_..."

* string2range
	*
	* 将字符串形式的域转化为数组形式的,(数组中为string类型的数字)
	*
	* @param string $range_string	一个值域，形如"a,b" 表示[a,b]; "a,b_c,d"表示[a,b]并[c,d]
	* @return array(array(a,b),array(c,d),...) ，空字符串将会返回 array(array())即，包含一个空数组的数组

* range2string
	*
	* 将数组形式的域转化为字符串形式的
	*
	* @param array	包含若干域的数组，空的域用空数组表示
	* @return 一个值域的字符串形式，形如"a,b" 表示[a,b]; "a,b_c,d"表示[a,b]并[c,d]
