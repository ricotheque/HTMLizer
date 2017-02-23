# HTMLizer
An object-oriented PHP Class that facilitates HTML integration.

##Why Use HTMLizer?
* Keeps your PHP code neat, and the HTML you generate readable.
* Creates HTML without spaces between tags, which makes your web pages smaller and solves at least [one CSS problem](https://css-tricks.com/fighting-the-space-between-inline-block-elements/).
* It can parse properly-formatted maps into HTML.

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
 
###Allow Empty Content: _build_empty
 
Create an HTML element even with empty content by adding the `_build_empty` switch anywhere in the tag name.

```php
/**
 * Generates
 * <div class="my-class" id="my-id"></div>
 */
echo \HTML::div_build_empty('class', 'my-class', 'id', 'my-id', '');
```

###Use Single Quotes: _single_quote

Some libraries (like [Flickity](http://flickity.metafizzy.co/#initialize-with-html)) require attributes wrapped in single quotes, not double. Use the `_single_quote` switch.

```php
/**
 * Generates
 * <div class='my-class' id='my-id'>This is some content</div>
 */
echo \HTML::div_single_quote('class', 'my-class', 'id', 'my-id', 'This is some content');
```

###Combining switches

Yes, you can combine the `_build_empty` and `_single_quote` switches, in any order.

```php
// Generates the same HTML
echo \HTML::div_single_quote_build_empty('class', 'my-class', 'id', 'my-id', '');
echo \HTML::div_build_empty_single_quote('class', 'my-class', 'id', 'my-id', '');
```

##Alternate Use
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

TO DO: Documentation on creating groups of HTML elements.
