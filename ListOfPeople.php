<?php

namespace App;

require_once('DB.php');

use Exception;
use PDO;
use App\DB;

if (class_exists('App\DB'))
{

    class ListOfPeople
    {
        public array $idOfPeople;

        public ?string $operator;
        public ?int $int;

        private array $errorMessages = [
            'constructError' => "оператор('<', '>', '!='), id(с которым сравниваем), ?Имя, ?Фамилия, ?Дата рождения, ?Пол, ?Город рождения.Прим.: ('>', 23, null, null, '2003-03-01')"
        ];


        public function __construct(
            string|null $operator = null,
            int|null $int = null,
            string|null $name = null,
            string|null $surname = null,
            string|null $birthday = null,
            int|null $gender = null,
            string|null $cityOfBirth = null
            )
        {

            $this->operator = $operator;
            $this->int = $int;

            if($name !== null)
            {
                $this->idOfPeople = $this->search('name', $name);

            }
            elseif($surname !== null)
            {
                $this->idOfPeople = $this->search('surname', $surname);

            }
            elseif($birthday !== null)
            {
                $this->idOfPeople = $this->search('birthday', $birthday);

            }
            elseif($gender !== null)
            {
                $this->idOfPeople = $this->search('gender', $gender);

            }
            elseif($cityOfBirth !== null)
            {
                $this->idOfPeople = $this->search('city_of_birth', $cityOfBirth);

            }
            else
            {
                throw new Exception($this->errorMessages['constructError']);
            }
        }

        public function getObject(): array
        {
            $result = [];
            foreach($this->idOfPeople as $value)
            {
                $result[]= (new DB($value))->humanFormatting();
            }
            return $result;
        }

        public function destroy(): void
        {
            foreach($this->idOfPeople as $value)
            {
                (new DB($value))->destroy();
            }
        }

        public function search(mixed $column, mixed $value): array|false
        {
            $pdo = DB::connectDB();
            $stmt = $pdo->prepare("SELECT id FROM users WHERE $column = :value AND id $this->operator $this->int");
            $stmt->execute([':value' => $value]);
            $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $pdo = null;

            return $result;
        }
    }
}
else
{
    throw new Exception("Класс 'DB' не подключен, пожалуйста проверте файл ListOfPeople.php");
}
