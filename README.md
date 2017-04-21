# HTMLizer
An object-oriented PHP Class that facilitates HTML creation.


## Why Use HTMLizer?
* Keeps your PHP code neat, and the HTML you generate readable.
* Creates HTML without spaces between tags, resulting in smaller markup that solves at least [one CSS problem](https://css-tricks.com/fighting-the-space-between-inline-block-elements/).
* It can parse properly-formatted arrays into HTML, allowing for more dynamic content generation.


## Sample Uses

It's good practice to output an HTML element only if it will wrap content. This requires an `if` check.

```php
if ($title) {
    echo "<h1>$title</h1>";
}
```

This can get cumbersome and hard to maintain if done multiple times:

```php
if ($title) {
    echo "<h1>$title</h1>";
}

if ($blurb) {
    echo "<p>$blurb</p>";
}
```

It gets even more complicated if you have to work with groups of elements:

```php
$html = '';

if ($title) {
    $html .= '<h1 class="article-title">' . $title . '</h1>';
}

if ($blurb) {
    $html .= '<p class="article-blurb">' . $blurb . '</p>';
}

if ($html) {
    $html = '<article class="article-box">' . $html . '</article>';
}
```

HTMLizer fixes this by returning an empty string if the specified content is empty.

```php
$h = new \HTML;
$title = '';
$blurb = '';

echo $h::article(
    'class', 'article-box',
    $h::h1('class', 'article-title', $title) . 
    $h::p('class', 'article-blurb', $blurb)
);
```

```html
<!-- Nothing -->
```

```php
$title = 'This is a title';
$blurb = 'This is a sample blurb';

echo $h::article(
    'class', 'article-box',
    $h::h1('class', 'article-title', $title) . 
    $h::p('class', 'article-blurb', $blurb)
);
```

```html
<div class="article-box"><h1 class="article-title">This is a title</h1><p class="article-blurb">This is a sample blurb</p></div>
```


## Creating HTML elements

Call the class with the tag name as the method, and specify any HTML attributes and content as parameters. This returns either an:

* HTML Element as a string: If the tag is self-closing, or if content was given.
* Empty string: If the tag has a closing tag, and no content was given.

Note how the content is always the last parameter.

```php
echo \HTML::div('class', 'my-class', 'This is some content');
```

```html
<div class="my-class">This is some content</div>
```

You can specify multiple attributes.

```php
echo \HTML::div('class', 'my-class', 'id', 'my-id', 'This is some content');
```

```html
<div class="my-class" id="my-id">This is some content</div>
```

For self-closing tags, any content parameter is ignored.

```php
echo \HTML::input('class', 'my-class', 'id', 'my-id', 'This is some content');
echo \HTML::input('class', 'my-class', 'id', 'my-id');
```

```html
<!-- Both result in... -->
<input class="my-class" id="my-id" />
```


## Empty Attributes

Need to create those fancy `data-whatever` attributes for your Javascript? Use an empty string.

```php
echo \HTML::div('class', 'my-class', 'id', 'my-id', 'data-whatever', '', 'This is some content');
```

```html
div class="my-class" id="my-id" data-whatever>This is some content</div>
```


## Switches


### Allow Empty Content: `_build_empty`
 
Force HTMLizer to create an HTML element even with empty content by adding the `_build_empty` switch anywhere in the tag name.

```php
echo \HTML::div_build_empty('class', 'my-class', 'id', 'my-id', '');
```

```html
<div class="my-class" id="my-id"></div>
```

### Wrap Attribute Values in Single Quotes: `_single_quote`

Some libraries (like [Flickity](http://flickity.metafizzy.co/#initialize-with-html)) require that attributes be wrapped in single quotes. This is possible through the `_single_quote` switch, which like `_build_empty` can be used anywhere in the tag name.

```php
echo \HTML::div_single_quote('class', 'my-class', 'id', 'my-id', 'This is some content');
```

```html
<div class='my-class' id='my-id'>This is some content</div>
```

### Combining Switches

You can combine the `_build_empty` and `_single_quote` switches, in any order.

```php
echo \HTML::div_single_quote_build_empty('class', 'my-class', 'id', 'my-id', '');
echo \HTML::div_build_empty_single_quote('class', 'my-class', 'id', 'my-id', '');
```

```html
<!-- Both generate... -->
<div class='my-class' id='my-id'></div>
```

## Alternate Usage

### Array Parameter

HTMLizer also accepts arrays of `$name => $value` pairs. The content is specified through the `'%content%'` key. Useful for making your HTML even more dynamic!

```php
// Generates the same HTML
echo \HTML::div(array(
    'class' => 'my-class',
    'id' => 'my-id',
    '%content%' => 'This is some content',
));

echo \HTML::div('class', 'my-class', 'id', 'my-id', 'This is some content');
```

Yes, [switches](#switches) (`div_single_quote(array());`) will still work.

### Using Different PHP Structures

Any valid PHP array structure is acceptable, especially if it improves code readability.

```php
// All three calls generate the same HTML.

echo \HTML::div(array(
    'class' => 'my-class',
    'id' => 'my-id',
    '%content%' => 'This is some content',
));

echo \HTML::div([
    'class' => 'my-class', 'id' => 'my-id',
    '%content%' => 'This is some content',
]);

echo \HTML::div('class', 'my-class', 'id', 'my-id', 'This is some content');
```

### Instantiation

As a PHP Class, HTMLizer can be instantiated into a short reusable variable for more readable code.

```php
$h = new \HTML;
echo $h::div('class', 'my-class', 'id', 'my-id', 'This is some content');
echo $h::p('class', 'paragraph', 'It was a dark and stormy night, at least according to the beginning of the book.');
```

### Nesting

You can nest the same HTMLizer instance, using standard PHP array structures. Just watch your commas!

```php
$h = new \HTML;
echo $h::div(
    'class', 'my-class', 
    'id', 'my-id',
    $h::p(
        'class', 'paragraph',
        'It was a dark and stormy night, at least according to the beginning of the book.'
    )
);

echo $h::div([
    'class' => 'my-class', 
    'id' => 'my-id',
    '%content%' => $h::p([
        'class' => 'paragraph',
        '%content%' => 'It was a dark and stormy night, at least according to the beginning of the book.'
    ])
);
    
```

## Creating Groups 

You can create groups of HTML elements through the `group` method, nesting sub-elements inside the `'%content%'` key. Use this sample structure as a guide:

```php
array(
    'tag' => array(
        'attribute_name_1' => 'attribute_value',
        'attribute_name_2' => 'attribute_value',
        '%content%' => 'This is sample content',
    ),
    'tag_2' => array(
        'attribute_name_1' => 'attribute_value',
        'attribute_name_2' => 'attribute_value',
        '%content%' => array(
            'sub_tag_1' => array(
                'attribute_name_1' => 'attribute_value',
                'attribute_name_2' => 'attribute_value',
                '%content%' => 'Nested content',
            ),
            'sub_tag_2' => array(
                'attribute_name_1' => 'attribute_value',
                'attribute_name_2' => 'attribute_value',
                '%content%' => 'Nested content',
            ),
        ),
    ),
);
```

### Examples

```php
echo \HTML::group(array(
    'article' => array(
        'class' => 'article-box',
        '%content%' => array(
            'h1' => array(
                'class' => 'article-title',
                '%content%' => array(
                    'a' => array(
                        'href' => 'http://google.com',
                        '%content%' => 'This is the Article Headline',
                    ),
                ),
            ),
            'p' => array(
                'class' => 'article-blurb',
                '%content%' => 'What happens if we use longer text as a sample? Will it serve our purpose of demonstration?',
            ),
            
        ),
    ),
));
```

```html
<article class="article-box"><h1 class="article-title"><a href="http://google.com">This is the Article Headline</a></h1><p class="article-blurb">What happens if we use longer text as a sample? Will it serve our purpose of demonstration?</p></article>
```

Again, you can use your desired PHP array structure to match your own coding standards.

```php
echo \HTML::group([
    'article' => [
        'class' => 'article-box',
        '%content%' => [
            'h1' => [
                'class' => 'article-title',
                '%content%' => [
                    'a' => ['href' => 'http://google.com', '%content%' => 'This is the Article Headline'],
                ],
            ],
            'p' => [
                'class' => 'article-blurb',
                '%content%' => 'What happens if we use longer text as a sample? Will it serve our purpose of demonstration?',
            ],
        ],
    ],
]);
```

### Using the Same Tag Repetitively

Since PHP arrays must have unique keys, you can add a numeric switch (like `_1`) after the tag names to differentiate them. These are ignored when the HTML element is built.

```php
 echo \HTML::group(array(
    'article' => array(
        'class' => 'article-box',
        '%content%' => array(
            'h1' => array(
                'class' => 'article-title',
                '%content%' => array(
                    'a' => array(
                        'href' => 'http://google.com',
                        '%content%' => 'This is the Article Headline',
                    ),
                ),
            ),
            'p_1' => array(
                'class' => 'article-blurb',
                '%content%' => 'What happens if we use longer text as a sample? Will it serve our purpose of demonstration?',
            ),
            'p_2' => array(
                'class' => 'article-meta',
                '%content%' => 'February 23, 2017',
            ),
        ),
    ),
));
```

```html
<article class="article-box"><h1 class="article-title"><a href="http://google.com">This is the Article Headline</a></h1><p class="article-blurb">What happens if we use longer text as a sample? Will it serve our purpose of demonstration?</p><p class="article-meta">February 23, 2017</p></article>
```

You can combine these numeric switches with `_build_empty` and `_single_quote` as needed, again in any order.

```php
'p_2_single_quote' => array(
    ...
),
```
