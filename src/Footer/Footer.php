<?php

namespace WPScotty\WPSpock\Footer;

if (!defined('ABSPATH')) {
    exit;
}

class Footer
{

    public function menu($menu = false)
    {
        if ($menu && has_nav_menu($menu)) : ?>
          <nav class="footer-navigation"
               aria-label="<?php esc_attr_e('Footer Menu', 'wpspock'); ?>">
              <?php
              wp_nav_menu(
                  [
                      'theme_location' => $menu,
                      'menu_class' => 'footer-menu',
                      'depth' => 1,
                  ]
              );
              ?>
          </nav>
        <?php endif;

        return $this;
    }
}