# HTMLizer
An object-oriented PHP Class that facilitates HTML creation.


##Why Use HTMLizer?
* Keeps your PHP code neat, and the HTML you generate readable.
* Creates HTML without spaces between tags, which makes your web pages smaller and solves at least [one CSS problem](https://css-tricks.com/fighting-the-space-between-inline-block-elements/).
* It can parse properly-formatted maps into HTML, allowing for more dynamic content generation.

Typically, integrating HTML requires a lot of concatenation:

```php
if ($title) {
    echo "<h1>$title</h1>";
}
```

Which can get cumbersome if done multiple times:

```php
if ($title) {
    echo "<h1>$title</h1>";
}

if ($blurb) {
    echo "<p>$blurb</p>";
}
```

By default, HTMLizer doesn't create HTML tags if the specified content is empty.

```php
// Empty strings are echoed.
$title = '';
$blurb = '';
echo \HTML::h1($title);
echo \HTML::p($blurb);

/**
 * Echos
 * <h1>This is a title</h1>
 * <p>This is a sample blurb</p>
 */
$title = 'This is a title';
$blurb = 'This is a sample blurb';
echo \HTML::h1($title);
echo \HTML::p($blurb);
```


##Creating HTML elements

Use the name of the tag as the method name, and specify any HTML attributes and content as parameters. Note how the content
is always the last parameter.

```php
/**
 * Generates
 * <div class="my-class">This is some content</div>
 */
echo \HTML::div('class', 'my-class', 'This is some content');
```

You can specify multiple attributes.

```php
/**
 * Generates
 * <div class="my-class" id="my-id">This is some content</div>
 */
echo \HTML::div('class', 'my-class', 'id', 'my-id', 'This is some content');
```

For unary (self-closing) tags, any specified content is ignored.

```php
/**
 * Both generate
 * <input class="my-class" id="my-id" />
 */
echo \HTML::input('class', 'my-class', 'id', 'my-id', 'This is some content');
echo \HTML::input('class', 'my-class', 'id', 'my-id');
```


##Empty Attributes

Need to create those fancy `data-whatever` attributes? Use an empty string.

```php
/**
 * Generates
 * <div class="my-class" id="my-id" data-whatever>This is some content</div>
 */
echo \HTML::div('class', 'my-class', 'id', 'my-id', 'data-whatever', '', 'This is some content');
```


##Switches
 
###Allow Empty Content: `_build_empty`
 
Create an HTML element even with empty content by adding the `_build_empty` switch anywhere in the tag name.

```php
/**
 * Generates
 * <div class="my-class" id="my-id"></div>
 */
echo \HTML::div_build_empty('class', 'my-class', 'id', 'my-id', '');
```

###Use Single Quotes: `_single_quote`

Some libraries (like [Flickity](http://flickity.metafizzy.co/#initialize-with-html)) require attributes wrapped in single quotes, not double. Use the `_single_quote` switch.

```php
/**
 * Generates
 * <div class='my-class' id='my-id'>This is some content</div>
 */
echo \HTML::div_single_quote('class', 'my-class', 'id', 'my-id', 'This is some content');
```

###Combining Switches

Yes, you can combine the `_build_empty` and `_single_quote` switches, in any order.

```php
// Generates the same HTML
echo \HTML::div_single_quote_build_empty('class', 'my-class', 'id', 'my-id', '');
echo \HTML::div_build_empty_single_quote('class', 'my-class', 'id', 'my-id', '');
```

##Alternate Usage

###Array Parameter

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

###Instantiation

As a PHP Class, HTMLizer can be instantiated into a one-letter variable for even more readable code.

```php
$h = new \HTML;
echo $h::div('class', 'my-class', 'id', 'my-id', 'This is some content');
echo $h::p('class', 'paragraph', 'It was a dark and stormy night, at least according to the beginning of the book.');
```

###Nesting

You can nest the same HTMLizer instance. Just watch your commas!

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
```

##Creating Groups 

You can create groups of HTML elements through the `group` method, nesting sub-elements inside the `'%content%'` key.

```php
/**
 * Generates
 * <article class="article-box"><h1 class="article-title"><a href="http://google.com">This is the Article Headline</a></h1><p class="article-blurb">What happens if we use longer text as a sample? Will it serve our purpose of demonstration?</p></article>
 */
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

###Using the Same Tag Repetitively

Since PHP arrays require unique keys, you can add a numeric switch (like `_1`) after the tag names to differentiate them. These are ignored when the HTML element is built.

```php
/**
 * Generates
 * <article class="article-box"><h1 class="article-title"><a href="http://google.com">This is the Article Headline</a></h1><p class="article-blurb">What happens if we use longer text as a sample? Will it serve our purpose of demonstration?</p><p class="article-meta">February 23, 2017</p></article>
 */
 
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

You can combine these numeric switches with `_build_empty` and `_single_quote` as needed, again in any order.

```php
'p_2_single_quote' => array(
    ...
),
```
