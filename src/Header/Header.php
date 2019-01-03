<?php

namespace WPScotty\WPSpock\Header;

if (!defined('ABSPATH')) {
    exit;
}

class Header
{

    public function class()
    {
        echo is_singular() && spock()->post()->canShowThumbnail() ? 'site-header featured-image' : 'site-header';

        return $this;
    }

    public function logo()
    {
        if (has_custom_logo()) : ?>
          <div class="site-logo"><?php the_custom_logo(); ?></div>
        <?php endif;

        return $this;
    }

    public function title()
    {
        $blog_info = get_bloginfo('name');

        if (!empty($blog_info)) {
            if (is_front_page() && is_home()) : ?>
              <h1 class="site-title">
                <a href="<?php echo esc_url(home_url('/')); ?>"
                   rel="home"><?php bloginfo('name'); ?>
                </a>
              </h1>
            <?php else : ?>
              <p class="site-title">
                <a href="<?php echo esc_url(home_url('/')); ?>"
                   rel="home"><?php bloginfo('name'); ?>
                </a>
              </p>
            <?php endif;
        }

        return $this;
    }

    public function description()
    {
        $description = get_bloginfo('description', 'display');

        if ($description || is_customize_preview()) : ?>
          <p class="site-description">
              <?php echo $description; ?>
          </p>
        <?php endif;

        return $this;
    }

    public function menu($menu = false)
    {
        if ($menu && has_nav_menu($menu)) : ?>
          <nav id="site-navigation"
               class="main-navigation"
               aria-label="<?php esc_attr_e('Top Menu', 'wpspock'); ?>">
              <?php
              wp_nav_menu(
                  [
                      'theme_location' => $menu,
                      'menu_class' => 'main-menu',
                      'items_wrap' => '<ul id="%1$s" class="%2$s" tabindex="0">%3$s</ul>',
                  ]
              );
              ?>
          </nav>
        <?php endif;

        return $this;
    }

    public function featureImage()
    {
        if (is_singular() && spock()->post()->canShowThumbnail()) : ?>
          <div class="site-featured-image">
              <?php spock()->post()->thumbnail(); ?>
          </div>
        <?php endif;
    }

}