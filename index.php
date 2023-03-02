<?php

namespace App;

require_once('ListOfPeople.php');

use Exception;


try
{

    // Задание 1



    //создание
    // $user = new DB(null, 'Миша', 'Иванов', '2003.03.02', 1, 'Минск');


    //отображение
    // $user = new DB(1);
    // echo $user->name;

    // echo '<pre>';
    // var_dump($user->humanFormatting());
    // echo '</pre>';


    //удаление
    // $user = new DB(1);
    // $user->destroy();




    //Задание 2


    //отображение
    // $list = new ListOfPeople('>', 1, null, null, null, null, 'Минск');
    // echo '<pre>';
    // var_dump($list->getObject());
    // echo '</pre>';

    //удаление
    // $list->destroy();

}
catch(Exception $exception)
{
    echo $exception->getMessage();
}
