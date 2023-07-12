<?php

return [
    'section' => [
        'heading' => [
            'detail' => 'Реквизиты для Юр. лица'
        ]
    ],
    'forms' => [
        'fieldset' => [
            'detail' => [
                'label' => 'Реквизиты для Юр. лица',
                'addButton' => 'Добавить реквизиты'
            ]
        ]
    ],
    'customer' => [
        'name' => 'Имя',
        'email' => 'Email',
        'phone' => 'Телефон',
        'person_type' => 'Выбор формы собственности',
        'created_at' => 'Создан',
        'updated_at' => 'Обновлен',
    ],
    'shiping'   => [
        'tab'   => [
            'general'       => 'Основное',
            'address'       => 'Aдрес',
            'work_time'     => 'Рабочее время',
            'cooler_and_tara'   => 'Кулер и тара',
            'balance'       => 'Баланс',
        ],
        'isMain'            => 'Основной адрес',
        'address_name'      => 'Краткое название',
        'fullname'          => 'ФИО',
        'firstname'         => 'Имя',
        'lastname'          => 'Фамилия',
        'email'             => 'Email',
        'phone'             => 'Телефон',
        'comment'           => 'Комментарий',

        'search_address'    => 'Поиск адреса (автозаполнение)',
        'search_address_description'    => 'Начните вводить адрес относительно Алматы или введите координаты',

        'full_address'      => 'Полный адрес',
        'country'           => 'Страна',
        'region'            => 'Регион',
        'district'          => 'Район',
        'locality'          => 'Город',
        'street'            => 'Улица',
        'house_number'      => 'Дом',
        'house_frontway'    => 'Подьезд',
        'house_floor'       => 'Этаж',
        'latitude'          => 'Широта',
        'longitude'         => 'Долгота',
        'type'              => 'type',

        'coords'            => 'Координаты',
        'apartment'                 => 'Офис/квартира',
        'intercom_code'             => 'Код домофона',

        'work_time' => [
            'start_at'          => 'Начало рабочего дня',
            'end_at'            => 'Конец рабочего дня',
            'launch_start_at'   => 'Обеденный перерыв с',
            'launch_end_at'     => 'Обеденный перерыв до',
            'weekend_days'      => 'Выходные дни',
        ],

        'cooler'    => [
            'having'    => 'Наличие кулера',
            'count'     => 'Количество кулеров на точке'
        ],
        'tara' => [
            'count'     => 'Количество тары на точке'
        ],

        'balance'       => [
            'label'     => 'Баланс точки',
            'balance'   => 'Баланс',
            'total_balance'   => 'Текущий баланс точки',
            'description'  => 'Основание',
            'created_at'   => 'Время',
        ],
    ],
    'detail'    => [
        'name'          => 'Юридическое название компании',
        'ownership'     => 'Форма собственности',
        'BIN_IIN'       => 'БИН/ИИН',
        'bank_account'  => 'Расчетный счет',
        'legal_address' => 'Юр. адрес',
        'KBE'           => 'КБЕ',
    ],

    'product'   => [
        'name'          => 'Наименование',
        'price'         => 'Цена',
        'quantity'      => 'Остаток',
        'type'          => 'Тип',
        'wp_id'         => 'ID продукта (WordPress)',
    ],

    'car'       => [
        'name'          => 'Название (марка авто)',
        'reg_name'      => 'Гос. номер',
    ],

    'individual_price'  => [
        'price'         => 'Спец. цена',
        'product_name'  => 'Продукт',
    ],

    'talons'            => [
        'section'       => [
            'title'     => 'Баланс талонов'
        ],
        'total_balance' => 'Текущий баланс талонов пользователя',

        'balance'       => 'Баланс',
        'description'   => 'Основание',
        'created_at'    => 'Создано',
    ],

    'courier'           => [
        'users'         => 'Сотрудник',
        'transport'     => 'Транспорт',
    ],
    'order'           => [
        'customer_shiping'         => 'Поиск адреса (автозаполнение)',
    ],
];
