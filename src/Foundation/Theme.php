<?php

/**
 * Say Hello,
 *  __    __  ___   __                  _
 * / / /\ \ \/ _ \ / _\_ __   ___   ___| | __
 * \ \/  \/ / /_)/ \ \| '_ \ / _ \ / __| |/ /
 *  \  /\  / ___/  _\ \ |_) | (_) | (__|   <
 *   \/  \/\/      \__/ .__/ \___/ \___|_|\_\
 *                    |_|
 *
 */

namespace WPScotty\WPSpock\Foundation;

use WPScotty\WPSpock\Database\WordPressOption;
use WPScotty\WPSpock\Footer\Footer;
use WPScotty\WPSpock\Header\Header;
use WPScotty\WPSpock\Post\Post;
use WPScotty\WPSpock\Support\Minifier;
use WPScotty\WPSpock\Support\MinifyHTML;
use WPScotty\WPSpock\Support\Str;

if (!defined('ABSPATH')) {
    exit;
}

class Theme
{

    /**
     * The get_template_directory(). You can use this path to "include" your files.
     *
     * @var $themePath
     */
    protected $themePath;

    /**
     * The get_template_directory_uri(). You can use this uri for scripts and styles.
     *
     * @var $themeUri
     */
    protected $themeUri;

    /**
     * The wp_get_theme() info.
     *
     * @var $theme
     */
    protected $theme;

    /**
     * Instance of Post class.
     *
     * @var Post
     */
    private $post = null;

    /**
     * Instance of Header class.
     *
     * @var Header
     */
    private $header = null;

    /**
     * Instance of Footer class.
     *
     * @var Footer
     */
    private $footer = null;

    /**
     * An instance of WordPress options class used to manage the options on the database.
     *
     * @var null
     */
    private $options_ = null;

    public function __construct()
    {
        $this->themePath = get_template_directory();
        $this->themeUri  = get_template_directory_uri();
        $this->theme     = wp_get_theme();

        $this->boot();
    }

    protected function boot()
    {
        add_action('after_setup_theme', function () {

            // load the theme configuration
            $theme = require get_template_directory() . '/config/theme.php';

            if (!empty($theme)) {

                // add_editor_style
                if (isset($theme['add_editor_style']) && $theme['add_editor_style']) {
                    add_editor_style();
                }

                // add_theme_support
                if (isset($theme['theme_support']) && is_array($theme['theme_support'])) {
                    foreach ($theme['theme_support'] as $key => $value) {
                        if (is_numeric($key)) {
                            add_theme_support($value);
                        } else {
                            add_theme_support($key, $value);
                        }
                    }
                }

                // Custom service provider
                if (isset($theme['providers'])) {
                    foreach ($theme['providers'] as $key => $className) {
                        $GLOBALS["spock_service_provider_{$key}"] = new $className;
                    }
                }

                // Minify HTML
                if (isset($theme['minify']) && $theme['minify']) {
                    MinifyHTML::init();
                }

                /**
                 * Make theme available for translation.
                 * Translations can be filed in the /languages/ directory.
                 */
                load_theme_textdomain('spock', get_template_directory() . '/languages');
            }

            // load the menu configuration
            $menu = require get_template_directory() . '/config/menus.php';

            // This theme uses wp_nav_menu() in one location.
            if (!empty($menu)) {
                register_nav_menus($menu);
            }

            // load the wordpress configuration
            $wordpress = require get_template_directory() . '/config/wordpress.php';

            if (!empty($wordpress)) {
                // admin bar
                if (isset($wordpress['show_admin_bar'])
                    && false === $wordpress['show_admin_bar'] && !is_admin()) {
                    add_action('init', function () {
                        add_filter('show_admin_bar', '__return_false');
                        wp_deregister_script('admin-bar');
                        wp_deregister_style('admin-bar');
                        remove_action('wp_footer', 'wp_admin_bar_render', 1000);
                        show_admin_bar(false);
                    });
                }

                // wordpress version
                if (isset($wordpress['wp_version'])
                    && false === $wordpress['wp_version']) {
                    remove_action('wp_head', 'wp_generator');
                    add_filter('the_generator', function () {
                        return '';
                    });
                }

                // clean wp_head
                if (!empty($wordpress['clean_wp_head'])) {
                    remove_action('wp_head', 'wp_resource_hints', 2);
                    remove_action('template_redirect', 'wp_shortlink_header', 11);
                    remove_action('wp_head', 'wlwmanifest_link');
                    remove_action('wp_head', 'rsd_link');
                    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
                    remove_action('wp_head', 'feed_links', 2);
                    remove_action('wp_head', 'feed_links_extra', 3);
                }

                // disable autentication by email
                if (isset($wordpress['authenticate_email_password'])
                    && false === $wordpress['authenticate_email_password']) {
                    remove_filter('authenticate', 'wp_authenticate_email_password', 20);
                }

                // remove the author
                if (isset($wordpress['comments']['author_link'])
                    && false === $wordpress['comments']['author_link']) {
                    remove_filter('get_comment_author_link', '__return_false');
                }

                // remove the author link
                if (isset($wordpress['comments']['author_link'])
                    && false === $wordpress['comments']['author_link']) {
                    remove_filter('get_comment_author_link', '__return_false');
                    add_filter(
                        'get_comment_author_link',
                        function ($return, $author) {
                            $return = $author;

                            return $return;
                        },
                        10,
                        2
                    );
                }

                // excerpt_length
                if (isset($wordpress['posts']['excerpt_length'])) {
                    $count = $wordpress['posts']['excerpt_length'];
                    add_filter(
                        'excerpt_length',
                        function ($words) use ($count) {
                            return $count;
                        },
                        99
                    );
                }

                // feed
                if (isset($wordpress['feed'])
                    && false === $wordpress['feed']) {
                    $spock_disable_feed_hoook = function () {
                        wp_die(_t('<h1>Feed not available, please visit our <a href="' . get_bloginfo('url') . '">Home Page</a>!</h1>'));
                    };

                    add_action('do_feed', $spock_disable_feed_hoook, 1);
                    add_action('do_feed_rdf', $spock_disable_feed_hoook, 1);
                    add_action('do_feed_rss', $spock_disable_feed_hoook, 1);
                    add_action('do_feed_rss2', $spock_disable_feed_hoook, 1);
                    add_action('do_feed_atom', $spock_disable_feed_hoook, 1);
                }

                // widget shortcode
                if (isset($wordpress['shortcode']) && $wordpress['feed']) {
                    add_filter('widget_text', 'do_shortcode');
                }
            }

            // load the editor configuration
            $editor = require get_template_directory() . '/config/editor.php';

            if (!empty($editor['editor-styles'])) {
                // Add support for editor styles.
                add_theme_support('editor-styles');

                // Enqueue editor styles.
                add_editor_style($editor['editor-styles']);
            }

            if (!empty($editor['editor-font-sizes'])) {
                add_theme_support('editor-font-sizes', $editor['editor-font-sizes']);

                $this->admin_style(function () use ($editor) {
                    ?>
<style type="text/css">
    <?php foreach ($editor['editor-font-sizes'] as $font) : ?>
    <?php echo '.has-' . $font['slug'] . '-font-size { font-size: ' . $font['size'] . 'px; }'; ?>
    <?php endforeach; ?>
</style><?php
                });
            }

            if (!empty($editor['editor-color-palette'])) {
                add_theme_support('editor-color-palette', $editor['editor-color-palette']);

                $this->admin_style(function () use ($editor) {
                    ?>
<style type="text/css">
    <?php foreach ($editor['editor-color-palette'] as $color) : ?>
    <?php echo '.has-text-color.has-' . $color['slug'] . '-color { color: ' . $color['color'] . '; }'; ?>

    <?php echo '.has-background.has-' . $color['slug'] . '-background-color { background-color: ' . $color['color'] . '; }'; ?>

    <?php endforeach; ?>
</style><?php
                });
            }

            if (!empty($editor['upload_mimes'])) {
                $upload_mimes = $editor['upload_mimes'];
                add_filter('upload_mimes', function ($mimes = []) use ($upload_mimes) {
                    return array_merge($mimes, $upload_mimes);
                });
            }
        });

        /**
         * Set the content width in pixels, based on the theme's design and stylesheet.
         *
         * Priority 0 to make it available to lower priority callbacks.
         *
         * @global int $content_width
         */

        add_action('after_setup_theme', function () {
            // This variable is intended to be overruled from themes.
            // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            $GLOBALS['content_width'] = apply_filters('spock_content_width', 640);
        }, 0);

        /**
         * Register widget area.
         *
         * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
         */
        add_action('widgets_init', function () {
            $sidebars = require get_template_directory() . '/config/sidebars.php';

            if (!empty($sidebars)) {
                foreach ($sidebars as $sidebar) {
                    register_sidebar($sidebar);
                }
            }
        });

        /**
         * Enqueue scripts and styles.
         */
        add_action('wp_enqueue_scripts', function () {
            $version = apply_filters('spock_developing', wp_get_theme()->version);

            // enqueue the main theme styles
            wp_enqueue_style('spock-style', get_stylesheet_uri(), [], $version);

            if (is_singular() && comments_open() && get_option('thread_comments')) {
                wp_enqueue_script('comment-reply');
            }

            $scripts = require get_template_directory() . '/config/scripts.php';

            if (!empty($scripts)) {
                foreach ($scripts as $key => $script) {
                    wp_enqueue_script($key, get_template_directory_uri() . "/public/js/{$script}", ['jquery'], $version, true);
                }
            }

            $styles = require get_template_directory() . '/config/styles.php';

            if (!empty($styles)) {
                foreach ($styles as $key => $style) {
                    wp_enqueue_style($key, get_template_directory_uri() . "/public/css/{$style}", [], $version, true);
                }
            }
        });

        /**
         * Used to add anything in the head
         */

        add_action('wp_head', function () {
            require get_template_directory() . '/resources/wp_head.php';
        });

        /**
         * Used to add anything in the footer
         */

        add_action('wp_footer', function () {
            require get_template_directory() . '/resources/wp_footer.php';
        });
    }

    /**
     * Return the WP Spock vendor folder
     *
     * @return string
     */
    protected function vendor(): string
    {
        return '/vendor/wpspock/wpspock/src/';
    }

    public function version(): string
    {
        return 'x.y.z';
    }

    /**
     * Return the Options theme instance used to get/set/delete options theme.
     * You'll be able to use spock()->options->get('myoption')
     *
     * @return \WPScotty\WPSpock\Database\WordPressOption|null
     */
    protected function getOptionsAttribute()
    {
        if (is_null($this->options_)) {
            $this->options_ = new WordPressOption($this);
        }

        return $this->options_;
    }

    /**
     * Return the slug of theme based on theme name.
     *
     * @return string
     */
    protected function getSlugAttribute()
    {
        return Str::snake($this->theme->Name);
    }

    public function __get($name)
    {
        $method = 'get' . Str::studly($name) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        if (property_exists($this->theme, $name)) {
            return $this->theme->{$name};
        }
    }

    /**
     * Load a theme resources.
     *
     * @param string $path Complete path/filename of resource.
     */
    public function resource(string $path)
    {
        $path = ltrim($path, '/');
        include "{$this->themePath}/resources/{$path}";
    }

    /**
     * Load a view from theme/resources/views
     *
     * @param string $name Name-path of the view.
     * @param array $data Data to be passed to view.
     */
    public function view(string $name, $data = [])
    {
        extract($data);
        $name = ltrim($name, '/');
        return include "{$this->themePath}/resources/views/{$name}";
    }

    /**
     * Import a file from theme.
     */
    public function import($path)
    {
        if (substr($path, 0, 1) === '/') {
            return include($this->themePath . $path);
        }

        return include($path);
    }

    /**
     * Import a Component from the theme.
     *
     * @param string $name Name of the component.
     */
    public function component($name)
    {
        return include("{$this->themePath}/theme/Components/{$name}.php");
    }

    /**
     * Return the css class string from an array of classes.
     *
     * @param array $classes Array of classes.
     * @param array $props Optional. Array of a component properties.
     * @return string
     *
     */
    public function cls($classes = [], $props = [])
    {
        $merged = array_map(function ($k, $v) {
            $value = is_array($v) ? implode('-', $v) : $v;
            $with_props = !is_object($value) ? "with-props-{$k}-value-{$value}" : "";
            return $v !== false ? "with-props-{$k} {$with_props}" : null;
        }, array_keys($props), array_values($props));
        $merged = array_merge($classes, $merged, $props['class'] ?? []);
        $merged = array_filter($merged);


        return trim(join(' ', $merged));
    }

    protected function minify_css(string $str): string
    {
        # remove comments first (simplifies the other regex)
        $re1 = <<<'EOS'
    (?sx)
    # quotes
    (
      "(?:[^"\\]++|\\.)*+"
    | '(?:[^'\\]++|\\.)*+'
    )
    |
    # comments
    /\* (?> .*? \*/ )
    EOS;

        $re2 = <<<'EOS'
    (?six)
    # quotes
    (
      "(?:[^"\\]++|\\.)*+"
    | '(?:[^'\\]++|\\.)*+'
    )
    |
    # ; before } (and the spaces after it while we're here)
    \s*+ ; \s*+ ( } ) \s*+
    |
    # all spaces around meta chars/operators
    \s*+ ( [*$~^|]?+= | [{};,>~+-] | !important\b ) \s*+
    |
    # spaces right of ( [ :
    ( [[(:] ) \s++
    |
    # spaces left of ) ]
    \s++ ( [])] )
    |
    # spaces left (and right) of :
    \s++ ( : ) \s*+
    # but not in selectors: not followed by a {
    (?!
      (?>
        [^{}"']++
      | "(?:[^"\\]++|\\.)*+"
      | '(?:[^'\\]++|\\.)*+'
      )*+
      {
    )
    |
    # spaces at beginning/end of string
    ^ \s++ | \s++ \z
    |
    # double spaces to single
    (\s)\s+
    EOS;

        $str = preg_replace("%$re1%", '$1', $str);
        return preg_replace("%$re2%", '$1$2$3$4$5$6$7', $str);
    }

    /**
     * Return a minified version of a inline css.
     *
     * @param string $css The css to minify.
     * @param bool $style_tag Optional. If true, return the css wrapped in a style tag.
     * @return string
     */
    public function css(string $str, $style_tag = false): string
    {
        $str = $this->minify_css($str);

        return $style_tag ? "<style type=\"text/css\">{$str}</style>" : $str;
    }

    /**
     * Return a minified version of a inline css.
     * This is the "ob" buffered version of css method.
     *
     * @param callable $callable The callable to be minified.
     * @param bool $script_tag Optional. If true, return the css wrapped in a style tag.
     * @return string
     */
    public function ob_css(callable $callable, $style_tag = false)
    {
        ob_start(function ($html) use ($style_tag) {
            return $this->css($html, $style_tag);
        });
        $callable();
        ob_end_flush();
    }

    /**
     * Add an inline style to the admin head.
     *
     * @param callable $callable The callable to be minified.
     * @param bool $script_tag Optional. If true, return the css wrapped in a style tag.
     *
     * @uses add_action('admin_head');
     */
    public function admin_style(callable $callable, $style_tag = false)
    {
        add_action('admin_head', function () use ($callable, $style_tag) {
            $this->ob_css($callable, $style_tag);
        });
    }

    /**
     * Returns a minified version of a inline js.
     *
     * @param string $js The js to minify.
     * @param array $options Optional. Array of options.
     */
    public function js(string $js, $options = []): string
    {
        return Minifier::minify($js, $options);
    }

    /**
     * Return a minified version of a inline js.
     * This is the "ob" buffered version of js method.
     *
     * @param string $js The js to minify.
     * @param array $options Optional. Array of options.
     */
    public function ob_js(callable $callable, $options = [])
    {
        ob_start();
        $callable();
        $js = ob_get_clean();
        echo $this->js($js, $options);
    }

    /**
     * Returns a minifier version of a inline html.
     *
     * @param string $html The html to minify.
     *
     * @return string
     */
    public function html(string $html): string
    {
        $search = [
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
            '/<!--(.|\s)*?-->/'
        ];

        $replace = [
            '>',
            '<',
            '\\1',
            ''
        ];

        $buffer = preg_replace($search, $replace, $html);

        return $buffer;
    }

    /**
     * Return a minified version of a inline html.
     * This is the "ob" buffered version of html method.
     *
     * @param callable $callable The callable to be minified.
     */
    public function ob_html(callable $callable)
    {
        ob_start(function ($html) {
            return $this->html($html);
        });
        $callable();
        ob_end_flush();
    }


    /**
     * Return an instance of a registered provider in the `config/theme.php`.
     *
     * @param string $key Provider key.
     * @return mixed|null
     */
    public function provider(string $key)
    {
        return $GLOBALS["spock_service_provider_{$key}"]??null;
    }

    /**
     * Return an instance of post
     *
     * @return \WPScotty\WPSpock\Post\Post
     */
    public function post(): Post
    {
        if (!$this->post) {
            $this->post = new Post;
        }

        return $this->post;
    }

    /**
     * Return an instance of post
     *
     * @return \WPScotty\WPSpock\Header\Header
     */
    public function header(): Header
    {
        if (!$this->header) {
            $this->header = new Header;
        }

        return $this->header;
    }

    /**
     * Return an instance of footer
     *
     * @return \WPScotty\WPSpock\Footer\Footer
     */
    public function footer(): Footer
    {
        if (!$this->footer) {
            $this->footer = new Footer;
        }

        return $this->footer;
    }

    /**
     * Helper
     *
     */
    public function theSlug()
    {
        global $post;

        echo $post->post_name ?? "";
    }

    /**
     * Wrapper of get_theme_mod() function.
     *
     * @param string         $name    Modification/setting name.
     * @param string|boolean $default Optional. Default value.
     * @return string
     */
    public function mod($name, $default = false)
    {
        return get_theme_mod($name, $default);
    }
}
