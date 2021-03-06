<?php
// конфигурация для русской типографики и фильтрации тегов

return [
    // настройка типографирования; если передать пустой массив или FALSE, то типографирование отключается
    'typography' => [
        // замена минуса между словами на нормальное тире; можно указать сам символ тире
        'replace_dashes' => true,
        // замена кавычек
        'replace_quotes' => true,
        // проверка пробелов вокруг знаков пунктуации
        'punctuation_space' => true,
    ],
    // автозамена символов
    'auto_replace' => [
        '+/-' => '±',
        '(c)' => '©',
        '(с)' => '©',
        '(C)' => '©',
        '(С)' => '©',
        '(r)' => '®',
        '(R)' => '®',
        '...' => '…',
    ],
    // обработка символов переводов строк
    'eol' => [
        // автозамена символов переводов строк на тег <br> (!fix)
        'auto_br' => true,
        // сохранение переводов строки (!new)
        'save' => true,
        // сколько переводов строки может идти подряд (!new)
        'limit' => 2,
        // символ перевода строки в итоговом тексте
        'char' => "\n",
    ],
    // обработка ссылок
    'links' => [
        // Включает или выключает режим автоматического определения ссылок
        'auto' => 0, //true,
        // список разрешенных протоколов для ссылок (https, http, ftp)
        'protocols' => ['https','http'],
        // протокол по умолчанию
        'default' => 'http',
    ],
    // теги, которые необходимо вырезать вместе с содержимым (имеет более высокий приоритет, чем allowed_tags
    'forbidden_tags' => ['script', 'object', 'iframe', 'style'],
    // задает список тегов с параметрами
    'tags' => [
        'b' =>[],
        'i' =>[],
        'u' =>[],
        'a' =>[
            // разрешенные атрибуты для тегов. Значение по умолчанию - шаблон #text.
            // Разрешенные шаблоны #text, #int, #link, #regexp(...) (Например: "#regexp(\d+(%|px))")
            'attr' => [
                'title', 'href' => '#link',
                'rel' => '#text', 'target' => ['_blank'], 'download' => '#bool'],
            // обязательные атрибуты
            'required' => ['href'],
            // атрибуты тегов, которые будут добавляться автоматически
            // задается значение по умолчанию и признак, нужно ли перезаписывать значение, если оно уже задано
            'auto' => ['rel' => ['nofollow', true]],
        ],
        'img' =>[
            'short' => true,    // короткие теги
            // Если перед именем атрибута стоит "!", то это - обязательный атрибут
            'attr' => ['!src' => '#text', 'alt' => '#text', 'title', 'align' => ['right', 'left', 'center'], 'width' => '#int', 'height' => '#int'],
            // атрибуты тегов, которые будут добавляться автоматически,
            // задается значение по умолчанию
            'auto' => ['alt' => ''],
        ],
        'ul' =>[
            'no_auto_br' => true,   // теги, внутри которых не нужна авто-расстановка тегов перевода на новую строку
            'block'     => true,    // блочные теги, после которых не нужно добавлять дополнительный перевод строки
            'no_text'   => true,    // тег является только контейнером для дочерних тегов и не может содержать текст
            // задает разрешенные дочерние теги
            'children'  => [
                'li' => true, // значение true означает, что дочерний тег может быть только в родительском теге
            ],
        ],
        'ol' =>[
            'no_auto_br' => true,
            'block'     => true,
            'no_text'   => true, // тег является только контейнером для дочерних тегов и не может содержать текст
            // задает разрешенные дочерние теги
            'children'  => [
                'li' => true, // значение true означает, что дочерний тег может быть только в родительском теге
            ],
        ],
        'li' =>[],
        'br' =>[
            'short' => true,    // короткие теги
        ],
        'code' =>[
            'pre'   => true,    // преформатированные теги, в которых нужно всё заменять на HTML сущности
            'block' => true,    // блочные теги, после которых не нужно добавлять дополнительный перевод строки
            'no_typography' => true, // теги, в которых нужно отключить типографирование текста
        ],
        'pre' =>[
            'no_typography' => true, // теги, в которых нужно отключить типографирование текста
        ],
        'div' =>[
            'empty' => true,    // не короткие теги, которые могут быть пустыми и их не нужно из-за этого удалять
        ],
        'cut' =>[
            'short' => true,
            'root' => true,     // тег может быть только в корне документа и не может быть дочерним к другим тегам
        ],
    ],
    'callbacks' => [
        'tags' => [
            'code' => 'tag_code_build',
        ],
        'event' => [
            'img' => 'tag_img_event',
        ],
        'chars' => [
            '#' => 'tag_sharp_build',
            '@' => 'tag_at_build',
        ],
    ],
    // задает список разрешенных тегов, если этот параметр не задан, то разрешены все теги, заданные в параметре tags
    'allowed_tags' => [],
];

// EOF
