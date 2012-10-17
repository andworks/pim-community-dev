<?php
namespace Strixos\IcecatConnectorBundle\Service;

use Strixos\IcecatConnectorBundle\Extract\LanguagesExtract;
use Strixos\IcecatConnectorBundle\Extract\ProductExtract;
use Strixos\IcecatConnectorBundle\Extract\ProductsExtract;
use Strixos\IcecatConnectorBundle\Extract\SuppliersExtract;

use Strixos\IcecatConnectorBundle\Transform\LanguagesTransform;
use Strixos\IcecatConnectorBundle\Transform\ProductTransform;
use Strixos\IcecatConnectorBundle\Transform\ProductsTransform;
use Strixos\IcecatConnectorBundle\Transform\SuppliersTransform;

use Strixos\IcecatConnectorBundle\Load\BatchLoader;

/**
 * Connector service
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright Copyright (c) 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class ConnectorService
{
    // TODO extends abstract service

    protected $container;

    /**
     * Constructor
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function importSuppliers()
    {
        $extract = new SuppliersExtract();
        $extract->process();

        $loader = new BatchLoader($this->container->get('doctrine.orm.entity_manager'));
        $transform = new SuppliersTransform($loader);
        $transform->process();
    }

    public function importLanguages()
    {
        $extract = new LanguagesExtract();
        $extract->process();

        $loader = new BatchLoader($this->container->get('doctrine.orm.entity_manager'));
        $transform = new LanguagesTransform($loader);
        $transform->process();
    }

    public function importProducts()
    {
        $extract = new ProductsExtract();
        $extract->process();

        $transform = new ProductsTransform($this->container->get('doctrine.orm.entity_manager'));
        $transform->process();
    }

    public function importProduct($productId)
    {
        // get product from id and set prodId and supplier name
        $em = $this->container->get('doctrine.orm.entity_manager');
//         $dm = $this->container->get('doctrine.orm.document_manager');

        $product = $em->getRepository('StrixosIcecatConnectorBundle:Product')->find($productId);

        $prodId = $product->getProdId();
        $supplierName = $product->getSupplier()->getName();

//         echo '<hr />Product ID : '. $product->getId().' - '. $product->getProdId() .'<br />';

        // TODO : parcours des locales avec lesquelles on travaille
        $locales = array('fr');

        $extract = new ProductExtract();
        $transform = new ProductTransform($this->container->get('akeneo.catalog.model_producttype'));


        foreach ($locales as $locale) {
            $fp = $extract->process($prodId, $supplierName, $locale);
            $transform->process($fp);
        }
    }

    public function importProductsFromSupplier($supplier)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $products = $em->getRepository('StrixosIcecatConnectorBundle:Product')->findBySupplier($supplier);

        foreach ($products as $product) {
            $this->importProduct($product->getId());
        }
    }
}