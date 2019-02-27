<?php

namespace AkeneoTest\Pim\Enrichment\Integration\PQB\Sorter;

use Akeneo\Pim\Enrichment\Component\Product\Query\Sorter\Directions;
use AkeneoTest\Pim\Enrichment\Integration\PQB\AbstractProductQueryBuilderTestCase;

/**
 * As IDs are generated by doctrine on product saving, products should be
 * sortable in the order of their saving.
 *
 * @author    Damien Carcel (damien.carcel@akeneo.com)
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class IdSorterIntegration extends AbstractProductQueryBuilderTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->createProduct('foo', []);
        $this->createProduct('bar', []);
        $this->createProduct('baz', []);
        $this->createProduct('BARISTA', []);
        $this->createProduct('BAZAR', []);
    }

    public function testOperatorAscending()
    {
        $result = $this->executeSorter([['id', Directions::ASCENDING]]);
        $this->assert($result, ['foo', 'bar', 'baz', 'BARISTA', 'BAZAR']);
    }

    public function testOperatorDescending()
    {
        $result = $this->executeSorter([['id', Directions::ASCENDING]]);
        $this->assert($result, ['BAZAR', 'BARISTA', 'baz', 'bar', 'foo']);
    }

    /**
     * @expectedException \Akeneo\Pim\Enrichment\Component\Product\Exception\InvalidDirectionException
     * @expectedExceptionMessage Direction "A_BAD_DIRECTION" is not supported
     */
    public function testErrorOperatorNotSupported()
    {
        $this->executeSorter([['identifier', 'A_BAD_DIRECTION']]);
    }
}