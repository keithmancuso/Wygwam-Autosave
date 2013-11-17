<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Wygwam_autosave_ext
{
    var $name           = 'Wygwam Autosave';
    var $version        = '0.1';
    var $description    = 'Adds the CKeditor autosave plugin to wygwam';
    var $docs_url       = 'http://www.familiar-studio.com/';
    var $settings_exist = 'n';
    
     private static $_included_resources = FALSE;

    private $_hooks = array(
        'wygwam_config',
    );

    public function activate_extension()
    {
        foreach ($this->_hooks as $hook)
        {
            ee()->db->insert('extensions', array(
                'class'    => get_class($this),
                'method'   => $hook,
                'hook'     => $hook,
                'settings' => '',
                'priority' => 10,
                'version'  => $this->version,
                'enabled'  => 'y'
            ));
        }
    }

    public function update_extension($current = NULL)
    {
        return FALSE;
    }

    public function disable_extension()
    {
        ee()->db->where('class', get_class($this))->delete('extensions');
    }

    public function wygwam_config($config, $settings)
    {
        if (($last_call = ee()->extensions->last_call) !== FALSE)
        {
            $config = $last_call;
        }

        // Add our plugin to CKEditor
        if (!empty($config['extraPlugins']))
        {
            $config['extraPlugins'] .= ',';
        }

        $config['extraPlugins'] .= 'autosave';

        $this->_include_resources();

        return $config;
    }
    
    
    private function _include_resources()
    {
        // Is this the first time we've been called?
        if (!self::$_included_resources)
        {
            // Tell CKEditor where to find our plugin
            $plugin_url = URL_THIRD_THEMES.'wygwam_autosave/autosave/';
            ee()->cp->add_to_foot('<script type="text/javascript">CKEDITOR.plugins.addExternal("autosave", "'.$plugin_url.'");</script>');

            // Don't do that again
            self::$_included_resources = TRUE;
        }
    }
}