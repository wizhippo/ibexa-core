<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\Core\ApiLoader;

use Ibexa\Bundle\Core\ApiLoader\Exception\InvalidSearchEngine;
use Ibexa\Bundle\Core\ApiLoader\Exception\InvalidSearchEngineIndexer;
use Ibexa\Contracts\Core\Container\ApiLoader\RepositoryConfigurationProviderInterface;
use Ibexa\Core\Search\Common\Indexer as SearchEngineIndexer;

/**
 * The search engine indexer factory.
 */
class SearchEngineIndexerFactory
{
    /**
     * Hash of registered search engine indexers.
     * Key is the search engine identifier, value indexer itself.
     *
     * @var \Ibexa\Core\Search\Common\Indexer[]
     */
    protected $searchEngineIndexers = [];

    public function __construct(
        private readonly RepositoryConfigurationProviderInterface $repositoryConfigurationProvider,
    ) {
    }

    /**
     * Registers $searchEngineIndexer as a valid search engine indexer with identifier $searchEngineIdentifier.
     *
     * note: It is strongly recommended to register indexer as a lazy service.
     *
     * @param \Ibexa\Core\Search\Common\Indexer $searchEngineIndexer
     * @param string $searchEngineIdentifier
     */
    public function registerSearchEngineIndexer(SearchEngineIndexer $searchEngineIndexer, $searchEngineIdentifier)
    {
        $this->searchEngineIndexers[$searchEngineIdentifier] = $searchEngineIndexer;
    }

    /**
     * Returns registered search engine indexers.
     *
     * @return \Ibexa\Core\Search\Common\Indexer[]
     */
    public function getSearchEngineIndexers()
    {
        return $this->searchEngineIndexers;
    }

    /**
     * Build search engine indexer identified by its identifier (the "alias" attribute in the service tag),
     * resolved for current SiteAccess.
     *
     * @throws \Ibexa\Bundle\Core\ApiLoader\Exception\InvalidSearchEngineIndexer
     *
     * @return \Ibexa\Core\Search\Common\Indexer
     */
    public function buildSearchEngineIndexer(): SearchEngineIndexer
    {
        $repositoryConfig = $this->repositoryConfigurationProvider->getRepositoryConfig();

        $searchEngineAlias = $repositoryConfig['search']['engine'] ?? null;
        if (null === $searchEngineAlias) {
            throw new InvalidSearchEngine(
                sprintf(
                    'Ibexa "%s" Repository has no Search Engine configured',
                    $this->repositoryConfigurationProvider->getCurrentRepositoryAlias()
                )
            );
        }

        if (!isset($this->searchEngineIndexers[$searchEngineAlias])) {
            throw new InvalidSearchEngineIndexer(
                "Invalid search engine '{$searchEngineAlias}'. " .
                "Could not find any service tagged with 'ibexa.search.engine.indexer' " .
                "with alias '{$searchEngineAlias}'."
            );
        }

        return $this->searchEngineIndexers[$searchEngineAlias];
    }
}
