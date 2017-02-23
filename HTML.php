<?php

class HTML
{

    /**
     * Parses an array map into a group of HTML elements.
     * 
     * array(
     *      'id' => array(
     *          'tag' => 'input',
     *          'attrs' => array(
     *          )
     *      )
     * );
     * 
     * @param   array   $elements   HTML element map.
     */
    public static function group($elements)
    {
        // Start with no HTML.
        $html = '';

        foreach ($elements as $tag => $args) {

            /**
             * If the %content attribute exists, and is an array,
             * this $tag has sub-elements. Resolve them into
             * a string first.
             */
            if (
                (isset($args['%content%'])) and 
                (is_array($args['%content%']))
            ) {
                $args['%content%'] = self::group($args['%content%']);
            }

            // Resolve the current $tag into HTML
            $html .= self::$tag($args);
        }

        return $html;
    }
    
    /**
    * Creates an HTML element. 
    * 
    * @param   string   $tag    Tag name.
    * @param   string   $args   Variable number list of tag attributes and content.
    * 
    * @return                   HTML element, or empty string if 
    */
    public static function __callStatic($tag, $args)
    {
        // Start with no HTML.
        $html = '';

        // Parse the tag for switches and properties.
        $tag_check = self::parseTag($tag);
        $tag = $tag_check[0];
        $build_empty = $tag_check[1];
        $single_quote = $tag_check[2];
        $unary = $tag_check[3];

        // Parse the arguments into an attribute and content array.
        self::parseArgs($args);

        /**
         * For non self-closing tags, check if we still have to build the 
         * HTML even if the content is empty.
         */
        if (!$build_empty and !$unary) {
            if (
                (!isset($args['%content%'])) or 
                (
                    (!$args['%content%']) and 
                    ('0' !== $args['%content%'])
                )
            ) {
                return '';
            }
        }


        // Create the opening HTML tag.
        $html = "<$tag";

        // Add the attributes
        foreach ($args as $name => $value) {
            
            // Ignore any content first.
            if ('%content%' == $name) {
                continue;
            }

            // Add the attribute name.
            $html .= " $name";

            // Add the attribute value.
            if (
                ($value) or 
                ('0' === $value)
            ) {
                // Use double or single quotes as specified.
                if ($single_quote) {
                    $html .= "='$value'";
                } else {
                    $html .= '="' . $value . '"';
                }
            }
        }

        // For self-closing tags, close it.
        if ($unary) {
            $html .= ' />';

        // For non-self-closing tags, add the content (if existing) and the closing tag.
        } else {
            if (!isset($args['%content%'])) {
                $content = '';
            } elseif (is_array($args['%content%'])) {
                $content = implode($args['%content%']);
            } else {
                $content = $args['%content%'];
            }
            $html .= '>' . $content . "</$tag>";
        }

        // Return the HTML.
        return $html;
    }

    /**
     * Parses a tag name, to check if the _build_empty or
     * _single_quote switches are specified.
     * 
     * @param   string   $tag   Tag to check.
     * @return  array           Array containing:
     *                          0 => Tag name with all switches removed.
     *                          1 => Boolean: Presence of _build_empty switch.
     *                          2 => Boolean: Presence of _single_quote switch.
     *                          3 => Boolean: If tag is self-closing or not.
     */
    public static function parseTag($tag)
    {
        /**
         * To create compatibility for self::group() method:
         * - Strip any number-based '_XXXXX' switches
         * - Strip any instance of '_group'
         */
        $pattern = '/_+[0-9]+/';
        $tag = preg_replace($pattern, '', $tag);
        $tag = str_replace('_group', '', $tag);

        /**
         * If the tag contains '_build_empty', this means we still build
         * non self-closing tags with empty content.
         */
        if (false !== strpos($tag, '_build_empty')) {
            $tag = str_replace('_build_empty', '', $tag);
            $build_empty = true;
        } else {
            $build_empty = false;
        }

        /**
         * If the tag contains '_single_quote', this means we build 
         * tag attribute values with single quotes, instead of double.
         */
        if (false !== strpos($tag, '_single_quote')) {
            $tag = str_replace('_single_quote', '', $tag);
            $single_quote = true;
        } else {
            $single_quote = false;
        }

        /**
        * Check if the tag is self-closing
        * @link   https://www.quora.com/Which-HTML-tags-are-self-closing
        */
        $self = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr');
        $unary = in_array($tag, $self);        

        return array($tag, $build_empty, $single_quote, $unary);
    }    

    /**
     * Parses an argument list into a one-dimensional array of
     * HTML attributes and content.
     * 
     * @param   array   $args   Argument list. Passed by reference.
     */
    private static function parseArgs(&$args)
    {
        // Start with an empty attribute list and content.
        $final = null;

        // Count the number of items in the argument array.
        $count = count($args);

        /**
        * If the arguments are a one-dimensional array, convert
        * it into a two-dimensional array, with name => value pairs.
        * 
        * @link   http://stackoverflow.com/questions/9678290/check-if-an-array-is-multi-dimensional/9678409#9678409
        */
        if ($count == count($args, COUNT_RECURSIVE)) {
            $i = 0;

            foreach ($args as $arg) {
                $i++;

                // If the current item is odd-numbered, it's an attribute name.
                if ($i & 1) {
                    $name = $arg;

                // Otherwise, it's an attribute value.
                } else {
                    $value = $arg;
                }

                /**
                 * If the current item is odd-numbered, and it's the last item,
                 * convert it into the content.
                 */
                if (($i & 1) and ($count == $i)) {
                    $final['%content%'] = $name;
                }

                /**
                 * If the current item is even-numbered, create the attribute
                 * name => value pair.
                 */
                if (0 == $i % 2) {
                    $final[$name] = $value;
                }
            }

        /**
         * Otherwise, flatten the arguments array (since variable arguments
         * are always contained in an array).
         */
        } else {
            $final = $args[0];
        }

        // Save the parsed arguments
        if (null === $final) {
            $final = [];
        }
        $args = $final;
    }

}
