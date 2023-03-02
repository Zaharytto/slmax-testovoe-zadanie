<?php

namespace App;

use Exception;
use StdClass;
use PDO;

class DB
{
    public ?int $id;
    public ?string $name;
    public ?string $surname;
    public ?string $birthday;
    public ?bool $gender;
    public ?string $cityOfBirth;

    const USER = 'root';
    const PASSWORF = 'root';
    const HOST = 'localhost';
    const DB_NAME = 'mydb';

    private array $errorMessages = [
        'id' => "Неудалось найти пользователя или укажите все поля для регистрации. Для регистрации нового пользователя укажите: 'null', 'Имя', 'Фамилия', 'год рождения', 'пол('1' - муж., '0' - жен.)', 'Город рождения'",
        'name' => 'Некорректно указано Имя нового пользователя. Пожалуйста пишите буквами',
        'surname' => 'Некорректно указана Фамилия нового пользователя. Пожалуйста пишите буквами',
        'birthday' => "Некорректно указана Дата рождения нового пользователя. Пожалуйста напишите так (Прим. '2003.03.14')",
        'gender' => "Некорректно указан Пол нового пользователя. Пожалуйста напишите: '1' - муж., '0' - жен.",
        'cityOfBirth' => 'Некорректно указан Город рождения нового пользователя. Пожалуйста пишите буквами'
    ];


    public function __construct($id = null, $name = null, $surname = null, $birthday = null, $gender = null, $cityOfBirth = null)
    {
        $this->id = is_int($id) || is_null($id) ? $id : throw new Exception($this->errorMessages['id']);
        $this->name = is_string($name) || is_null($name) ? $name : throw new Exception($this->errorMessages['name']);
        $this->surname = is_string($surname) || is_null($surname) ? $surname : throw new Exception($this->errorMessages['surname']);
        $this->birthday = is_string($birthday) || is_null($birthday) ? $birthday : throw new Exception($this->errorMessages['birthday']);
        $this->gender = $gender === 0 || $gender === 1 || is_null($gender) ? $gender : throw new Exception($this->errorMessages['gender']);
        $this->cityOfBirth = is_string($cityOfBirth) || is_null($cityOfBirth) ? $cityOfBirth : throw new Exception($this->errorMessages['cityOfBirth']);

        if($this->id &&
            $this->name === null &&
            $this->surname === null &&
            $this->birthday === null &&
            $this->gender === null &&
            $this->cityOfBirth === null
            )
        {
            $pdo = self::connectDB();

            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->execute([':id' => $this->id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user !== false)
            {
                $this->id = $user['id'];
                $this->name = $user['name'];
                $this->surname = $user['surname'];
                $this->birthday = $user['birthday'];
                $this->gender = $user['gender'];
                $this->cityOfBirth = $user['city_of_birth'];
            }
            else
            {
                throw new Exception($this->errorMessages['id']);
            }


            $pdo = null;
        }
        elseif($this->id === null &&
            $this->name &&
            $this->surname &&
            $this->birthday &&
            $this->gender &&
            $this->cityOfBirth
            )
        {
            $this->save();
        }
        else
        {
            throw new Exception($this->errorMessages['id']);
        }
    }


    public function save(): void
    {
        $pdo = self::connectDB();

        $stmt = $pdo->prepare('INSERT INTO users(name, surname, birthday, gender, city_of_birth)
        value(:name, :surname, :birthday, :gender, :city_of_birth)');
        $stmt->execute([
            ':name' => $this->name,
            ':surname' => $this->surname,
            ':birthday' => $this->birthday,
            ':gender' => $this->gender,
            ':city_of_birth' => $this->cityOfBirth
        ]);
        $pdo = null;
    }

    public function destroy(): void
    {
        $pdo = self::connectDB();

        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
        $pdo = null;
    }

    public static function ageTransformation(string $date): int|float
    {
        $age = date('Y') - date('Y', strtotime($date));
        return $age = date('md', (int)$date) > date('md') ? $age-- : $age;
    }

    public static function genderTransformation(bool $gender): string
    {
        return $gender ? 'муж.' : 'жен.';
    }

    public function humanFormatting(): object
    {
        $class =  new StdClass();
        $class->id = $this->id;
        $class->name = $this->name;
        $class->surname = $this->surname;
        $class->age = self::ageTransformation($this->birthday);
        $class->gender = self::genderTransformation($this->gender);
        $class->cityOfBirth = $this->cityOfBirth;

        return $class;
    }

    public static function connectDB(): PDO
    {
        $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME;
        return new PDO($dsn, self::USER, self::PASSWORF);
    }
}
