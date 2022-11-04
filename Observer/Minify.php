<?php
/**
 * @category  Apptrian
 * @package   Apptrian_Minify
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 */

namespace Apptrian\Minify\Observer;

use Magento\Framework\Event\ObserverInterface;

class Minify implements ObserverInterface
{
    /**
     * @var \Apptrian\Minify\Helper\Data
     */
    public $helper;
    
    /**
     * Constructor
     *
     * @param \Apptrian\Minify\Helper\Data $helper
     */
    public function __construct(\Apptrian\Minify\Helper\Data $helper)
    {
        $this->helper = $helper;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $minifyEnabled = $this->helper->getMinifyEnabled();
        
        if ($minifyEnabled) {
            $response = $observer->getEvent()->getResponse();
            $html     = $response->getBody();

            if (stripos($html, '<!DOCTYPE html') !== false) {
                $type = true;
                
                $headers = $response->getHeaders()->toArray();
                
                if (array_key_exists('Content-Type', $headers)
                    && $headers['Content-Type'] == 'application/json'
                ) {
                    $type = false;
                }
                
                if ($type) {
                    $response->setBody(
                        $this->helper->minifyHtml(
                            $html,
                            $this->helper->getRemoveComments(),
                            $this->helper->getCacheCompatibility(),
                            $this->helper->getMaxMinification()
                        )
                    );
                }
            }
        }
    }
}
