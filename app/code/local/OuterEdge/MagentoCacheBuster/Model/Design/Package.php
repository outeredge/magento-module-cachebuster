<?php

if (class_exists('Fooman_SpeedsterAdvanced_Model_Core_Design_Package')) {
    class MiddleManClass extends Fooman_SpeedsterAdvanced_Model_Core_Design_Package {}
} else {
    class MiddleManClass extends Mage_Core_Model_Design_Package {}
}
class OuterEdge_MagentoCacheBuster_Model_Design_Package extends MiddleManClass
{
    protected $baseUrl;
    protected $extensions = array();
    protected $hashes = array();
    protected $hashfile = 'cachebuster.php';

    public function getSkinUrl($file = null, array $params = array())
    {
        $url = parent::getSkinUrl($file, $params);

        if (!in_array(pathinfo($url, PATHINFO_EXTENSION), $this->getExtensions())) {
            return $url;
        }

        return $this->getHash($url);
    }

    protected function getHash($url)
    {
        if (empty($this->hashes) && file_exists($this->hashfile)) {
            $this->hashes = include $this->hashfile;
        }

        if (!array_key_exists($url, $this->hashes)) {
            $path = str_replace($this->getBaseUrl(), Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_SKIN) . DS, $url);
            if (!file_exists($path)) {
                $this->hashes[$url] = $url;
            } else {
                $this->hashes[$url] = substr_replace($url, '.' . md5_file($path), strrpos($url, '.'), 0);
            }

            file_put_contents($this->hashfile, '<?php return ' . var_export($this->hashes, true) . ';');
        }

        return $this->hashes[$url];
    }

    protected function getExtensions()
    {
        if (Mage::getIsDeveloperMode()) {
            return array();
        }
        if (empty($this->extensions)) {
            $this->extensions = explode(PHP_EOL, Mage::getStoreConfig('cachebuster/settings/extensions'));
        }
        return $this->extensions;
    }

   protected function getBaseUrl()
   {
       if (null === $this->baseUrl) {
           $this->baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
       }
       return $this->baseUrl;
   }
}
