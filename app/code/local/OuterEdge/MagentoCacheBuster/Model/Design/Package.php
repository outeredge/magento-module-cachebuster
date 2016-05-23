<?php

class OuterEdge_MagentoCacheBuster_Model_Design_Package extends Mage_Core_Model_Design_Package
{
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
            if (!file_exists($url)) {
                $this->hashes[$url] = $url;
            } else {
                $this->hashes[$url] = substr_replace($url, '.' . md5_file(ltrim(parse_url($url, PHP_URL_PATH), '/')), strrpos($url, '.'), 0);
            }
            
            file_put_contents($this->hashfile, '<?php return ' . var_export($this->hashes, true) . ';');
        }

        return $this->hashes[$url];
    }

    protected function getExtensions()
    {
        if (empty($this->extensions)) {
            $this->extensions = explode(PHP_EOL, Mage::getStoreConfig('cachebuster/settings/extensions'));
        }
        return $this->extensions;
    }
}
