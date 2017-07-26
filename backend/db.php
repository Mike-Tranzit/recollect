<?php
/**
 * Created by PhpStorm.
 * User: Похотливая болонка
 * Date: 26.07.2017
 * Time: 22:23
 */
$user="all";
$pas="1111";
$host='192.168.2.104';
$link=mysqli_connect($host,$user,$pas);
mysqli_query($link,"SET NAMES 'cp1251'");
mysqli_query($link,"SET CHARACTER SET 'cp1251'");