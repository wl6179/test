<?php
/**
 * 显示调用栈
 * 根据栈的先进后出特点，即嵌套、递归的函数调用形式，测试的输出~只需要从下往上看就能理清函数a的调用步骤过程！！
 * 加上错误和异常处理中的相关函数，调试算是OK了。
 */
header("Content-type:text/html;charset=utf-8");

/**
 * a
 */
function a() {
	b();
}

/**
 * b
 */
function b() {
	c();
}

/**
 * c
 */
function c() {
    debug_print_backtrace();    #show 调用栈 e 大环境结构！！
}

#调用函数
a();