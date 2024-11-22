<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src\im\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Blur image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImBlurImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $type = $this->getType();

        switch ($type) {
            case 'adaptive':
                $container->getResource()->adaptiveBlurImage($this->getRadius(), $this->getSigma(), $this->getChannel());
                break;
            case 'motion':
                $container->getResource()->motionBlurImage($this->getRadius(), $this->getSigma(), $this->getAngle(), $this->getChannel());
                break;
            case 'radial':
                $container->getResource()->radialBlurImage($this->getAngle(), $this->getChannel());
                break;
            default:
                $container->getResource()->blurImage($this->getRadius(), $this->getSigma(), $this->getChannel());
        }
    }

    function getType()
    {
        return $this->getParam('type', '');
    }

    function getRadius()
    {
        return $this->getParam('radius', 5.0);
    }

    function getSigma()
    {
        return $this->getParam('sigma', 1.2);
    }

    function getAngle()
    {
        return $this->getParam('angle', 45.0);
    }

    function getChannel()
    {
        return $this->getParam('channel', \Imagick::CHANNEL_ALL);
    }
}
