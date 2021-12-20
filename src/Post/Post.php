<?php

namespace WPScotty\WPSpock\Post;

if (!defined('ABSPATH')) {
    exit;
}

class Post
{

    /**
     * Prints HTML with meta information for the current post-date/time.
     *
     * @return \WPScotty\WPSpock\Post\Post
     */
    public function postedOn(): Post
    {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        printf(
            '<span class="posted-on">%1$s<a href="%2$s" rel="bookmark">%3$s</a></span>',
            '🥎',
            esc_url(get_permalink()),
            $time_string
        );

        return $this;
    }

    /**
     * Prints HTML with meta information about theme author.
     *
     * @return \WPScotty\WPSpock\Post\Post
     */
    public function postedBy(): Post
    {
        printf(
            '<span class="byline">%1$s<span class="screen-reader-text">%2$s</span><span class="author vcard"><a class="url fn n" href="%3$s">%4$s</a></span></span>',
            '🧘‍',
            _t('Posted by'),
            esc_url(get_author_posts_url(get_the_author_meta('ID'))),
            esc_html(get_the_author())
        );

        return $this;
    }

    /**
     * Prints an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     *
     * @return \WPScotty\WPSpock\Post\Post
     */
    public function thumbnail(): Post
    {
        if (!$this->canShowThumbnail()) {
            return $this;
        }

        if (is_singular()) : ?>
          <figure class="post-thumbnail">
              <?php the_post_thumbnail(); ?>
          </figure>
        <?php else :
            ?>
          <figure class="post-thumbnail">
            <a class="post-thumbnail-inner"
               href="<?php the_permalink(); ?>"
               aria-hidden="true"
               tabindex="-1">
                <?php the_post_thumbnail('post-thumbnail'); ?>
            </a>
          </figure>
        <?php endif; // End is_singular().

        return $this;
    }

    /**
     * Determines if post thumbnail can be displayed.
     *
     * @return bool
     */
    public function canShowThumbnail(): bool
    {
        return apply_filters('wpspock_can_show_post_thumbnail', !post_password_required() && !is_attachment() && has_post_thumbnail());
    }

    /**
     * Prints the categories for the current post.
     *
     * @return \WPScotty\WPSpock\Post\Post
     */
    public function categories($args = []) : Post {

      $defaults = [
          'separator' => _t(', '),
          'icon' => '',
          'screen_reader' => _t('Posted in')
      ];

      $args = array_merge($args, $defaults);

        /**
         * @var $separator
         * @var $icon
         * @var $screen_reader
         */
      extract($args);

      // translators: used between list items, there is a space after the comma.
        $categories_list = get_the_category_list($separator);

        if ($categories_list) {
            printf(
                '<span class="cat-links">%1$s<span class="screen-reader-text">%2$s</span>%3$s</span>',
                $icon,
                $screen_reader,
                $categories_list
            ); // WPCS: XSS OK.
        }

        return $this;
    }

    /**
     * Prints the tags for the current post.
     *
     * @return \WPScotty\WPSpock\Post\Post
     */
    public function tags() : Post {
        // translators: used between list items, there is a space after the comma.
        $tags_list = get_the_tag_list('', _t(', '));
        if ($tags_list) {
            printf(
                '<span class="tags-links">%1$s<span class="screen-reader-text">%2$s </span>%3$s</span>',
                '🥰',
                _t('Tags:'),
                $tags_list
            ); // WPCS: XSS OK.
        }

        return $this;
    }

    /**
     * Prints the edit post link.
     *
     * @return \WPScotty\WPSpock\Post\Post
     */
    public function edit():Post {

        edit_post_link(
            sprintf(
                wp_kses(
                    _t('Edit <span class="screen-reader-text">%s</span>'),
                    [
                        'span' => [
                            'class' => [],
                        ],
                    ]
                ),
                get_the_title()
            ),
            '<span class="edit-link">🖋',
            '</span>'
        );

        return $this;
    }

    /**
     * Prints the "Leave a comment" link.
     *
     * @return \WPScotty\WPSpock\Post\Post
     */
    public function leaveComment():Post {
        if (!is_singular()) {
            // Prints HTML with the comment count for the current post.
            if (!post_password_required() && (comments_open() || get_comments_number())) {
                echo '<span class="comments-link">';
                comments_popup_link(sprintf(_t('Leave a comment<span class="screen-reader-text"> on %s</span>'), get_the_title()));

                echo '</span>';
            }
        }

        return $this;
    }

}