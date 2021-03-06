
## Qevix ![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/aVadim483/qevix)[![Build Status](https://travis-ci.org/aVadim483/qevix.svg?branch=master)](https://travis-ci.org/aVadim483/qevix)

**Qevix** — Система типографирования и фильтрации текста с HTML/XHTML разметкой .
При обработке HTML-текстов, применяя наборы правил, контролирует перечень допустимых тегов и атрибутов, предотвращает возможные XSS-атаки.

Qevix основывается на идеях и исходном коде [PHP версии Jevix](https://github.com/ur001/Jevix/).
Фильтр полностью переписан, устраняет ряд ошибок и недоработок, а также вводит новые возможности в правила фильтрации.

### Возможности

* Фильтрация текста с HTML/XHTML разметкой на основе заданных правил о разрешённых тегах и атрибутах;
* Исправление ошибок HTML/XHTML;
* Типографирование текстов без обработки HTML/XHTML
* Обработка строк предваренных специальными символами (#tagname, @username, $keyword);
* Установка на теги callback-функций для изменения или сбора информации;
* Предотвращение XSS-атак;

### Требования

* PHP >= 5.6
* php-mbstring
* UTF-8

### Подключение с помощью Composer

|$ composer require avadim/qevix

### Пример использования

```php
// Создаем экземпляр 
$qevix = new avadim\Qevix();

//  читаем файл конфигурации
$config = include 'path/to/file/config.php';

$qevix->setConfig($config);

// альтернативный вариант
$config = include 'path/to/file/config.php';
$qevix = new avadim\Qevix($config);

// еще более короткий вариант
$qevix = new avadim\Qevix('path/to/file/config.php');
```

```php
$qevix = new avadim\Qevix();

// Конфигурация - задаем параметры по отдельности

// 1. Задает список разрешенных тегов
$qevix->cfgSetTagsAllowed(['b', 'i', 'u', 'a', 'img', 'ul', 'li', 'ol', 'br', 'code', 'pre', 'div', 'cut']);

// 2. Указывает, какие теги считать короткими (<br>, <img>)
$qevix->cfgSetTagShort(['br','img','cut']);

// 3. Указывает преформатированные теги, в которых нужно всё заменять на HTML сущности
$qevix->cfgSetTagPreformatted(['code']);

// 4. Указывает не короткие теги, которые могут быть пустыми и их не нужно из-за этого удалять
$qevix->cfgSetTagIsEmpty(['div']);

// 5. Указывает теги, внутри которых не нужна авто-расстановка тегов перевода на новую строку
$qevix->cfgSetTagNoAutoBr(['ul', 'ol']);

// 6. Указывает теги, которые необходимо вырезать вместе с содержимым
$qevix->cfgSetTagCutWithContent(['script', 'object', 'iframe', 'style']);

// 7. Указывает теги, после которых не нужно добавлять дополнительный перевод строки. Например, блочные теги
$qevix->cfgSetTagBlockType(['ol','ul','code']);

// 8. Добавляет разрешенные параметры для тегов. Значение по умолчанию - шаблон #text. Разрешенные шаблоны #text, #int, #link, #regexp(...) (Например: "#regexp(\d+(%|px))")
$qevix->cfgSetTagAttrAllowed('a', ['title', 'href' => '#link', 'rel' => '#text', 'target' => ['_blank'], 'download' => '#bool']);
$qevix->cfgSetTagAttrAllowed('img', ['src' => '#text', 'alt' => '#text', 'title', 'align' => ['right', 'left', 'center'], 'width' => '#int', 'height' => '#int']);

// 9. Добавляет обязательные параметры для тега
$qevix->cfgSetTagAttrRequired('img', 'src');
$qevix->cfgSetTagAttrRequired('a', 'href');

// 10. Указывает, какие теги являются контейнерами для других тегов
$qevix->cfgSetTagChildren('ul', 'li', true, true);
$qevix->cfgSetTagChildren('ol', 'li', true, true);

// 11. Указывает, какие теги не должны быть дочерними к другим тегам
$qevix->cfgSetTagGlobal('cut');

// 12. Устанавливаем атрибуты тегов, которые будут добавляться автоматически
$qevix->cfgSetTagAttrDefault('a', 'rel', 'nofollow', true);
$qevix->cfgSetTagAttrDefault('img', 'alt', '');

// 13. Указывает теги, в которых нужно отключить типографирование текста
$qevix->cfgSetTagNoTypography(['code', 'pre']);

// 14. Устанавливает список разрешенных протоколов для ссылок (https, http, ftp)
$qevix->cfgSetLinkProtocolAllow(['http','https']);

// 15. Включает или выключает режим XHTML
$qevix->cfgSetXHTMLMode(false);

// 16. Включает или выключает режим автозамены символов переводов строк на тег br
$qevix->cfgSetAutoBrMode(true);

// 17. Включает или выключает режим автоматического определения ссылок
$qevix->cfgSetAutoLinkMode(true);

// 18. Задает символ/символы перевода строки. По умолчанию "\n". Разрешено "\n", "\r\n" или null (задает автоматически для текущей ОС)
$qevix->cfgSetEOL("\n");

// 19. Устанавливает на тег callback-функцию
$qevix->cfgSetTagBuildCallback('code', 'tag_code_build');

// 20. Устанавливает на строку предворенную спецсимволом (@|#|$) callback-функцию
$qevix->cfgSetSpecialCharCallback('#', 'tag_sharp_build');
$qevix->cfgSetSpecialCharCallback('@', 'tag_at_build');

// 21. Устанавливает на тег callback-функцию, которая сохраняет URL изображений для meta-описания
$qevix->cfgSetTagEventCallback('img', 'tag_img_event');

//-----

function tag_code_build($tag, $params, $content)
{
	return '<pre><code>'.$content.'<code><pre>'."\n";
}

//-----

$meta_img_src = array();

function tag_img_event($tag, $params, $content)
{
	global $meta_img_src;

	$meta_img_src[] = $params['src'];
}

//-----

function tag_sharp_build($string)
{
	if(!preg_match('#^[\w\_\-\ ]{1,32}$#isu', $string)) {
		return false;
	}

	return '<a href="/tag/'.rawurlencode($string).'/">#'.$string.'</a>';
}

//-----

function tag_at_build($string)
{
	if(!preg_match('#^[\w\_\-\ ]{1,32}$#isu', $string)) {
		return false;
	}

	return '<a href="/user/'.$string.'/">@'.$string.'</a>';
}

//-----

//Фильтр

$text = <<<EOD
Привет я <b>Qevix</b> и я много чего могу...
<!-- Могу удалить тег <h1>, впрочем, как и этот комментарий -->
<h1>Этого тега нет в разрешенных</h1>

<!-- Это пустой тег, его тоже не должно быть, хотя <div> мог бы быть -->
<p></p>
<div></div>

Отмечать метки #qevix, #php, #[mysql], #{mariaDB}, #[два слова].
Людей @Alexander @Андрей @[Илья Андреевич Ростов]

(Вы можете запретить определение меток из нескольких слов, просто проверяя это в callback функции)

Работать с разными тегами <s>Но не с этим ... <u>зачеркивать нельзя</u></s>,
<script type="text/javascript">alert('И скрипты писать нельзя')</script>
Так же я запрещаю iframe, style и object... но это согласно текущим правилам.

А такие теги сейчас разрешены: <b>Куда же без выделения жирным</b> или <i>курсива</i>...
<b>И <b>вложенность</b> не помеха</b>

Могу выделить код используя callback функцию:

<code>
	<body>
		<b>JavaScript:</b>
		<script>alert('Hello World')</script>
	</body>
</code>

Могу подсвечивать ссылки так https://github.com или так www.yandex.ru или в скобках (http://webonrails.ru)!
Могу найти ссылку в теге <b>http://webonrails.ru</b>.
Или ссылка может быть более сложной http://yandex.ru/yandsearch?lr=2&text=qevix!..
А также традиционная <a href="https://ru.wikipedia.org">Википедия</a>
Но только не опасная <a href="javascript:alert('Hi!')" title="Нажми на меня">Hello World!</a>
И без ненужных атрибутов <a href="https://github.com" name="top" hreflang="ru">GitHub</a>
Или вот такая <a href=http://php.net title = text target=_blank>Я могу определить атрибуты без кавычек!</a>

Использовать изображения <img src="http://php.net/images/news/phpday2012.png" alt="Image">

Могу менять "кавычки" на елочки и "соблюдать "вложенность" кавычек"...

Использовать преформатирование <pre>Могу менять "кавычки" на елочки и "соблюдать "вложенность" кавычек"</pre>

Убирать такой код:
<li>Пункт 1</li>
<li>Пункт 2</li>

И оставлять такой:

<ul>
  <li>Пункт 1</li>
  <li>Пункт 2</li>
</ul>

Могу удалить это верхний перевод строки, что бы текст под блочными элементами нормально отображался.

Преобразовывать коды символов &#40; &#41; &#42; &#43; &#44; обратно в символы.

Преобразовывать в тексте короткое тире - в длинное, но не в таком (2-2=0) и не в таком (веб-программирование)
Зато могу работать с пунктами (диалогами):
- Пункт 1
- Пункт 2
- Пункт 3

Могу делать правильный CUT: <b>Краткая часть</b> <cut> <b>Тег <cut> не может быть вложенным</b>

Могу    убирать    лишние пробелы и сам <b>закрывать <u>теги
EOD;

$result = $qevix->parse($text, $errors);

echo $result;
```

### Документация по конфигурации

* [DOCUMENTATION](DOCUMENTATION.md)

