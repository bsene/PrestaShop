<?php

/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\EntityTranslation;

use DataLangCore;
use Db;
use PrestaShop\PrestaShop\Core\Translation\EntityTranslatorInterface;
use PrestaShopBundle\Translation\TranslatorInterface;
use TabLang;

/**
 * Builds entity translators
 */
class EntityTranslatorFactory
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var DataLangFactory
     */
    private $dataLangFactory;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->db = Db::getInstance();
        $this->dataLangFactory = new DataLangFactory(_DB_PREFIX_, $translator);
        $this->translator = $translator;
    }

    /**
     * Builds an entity translator based on a table name
     *
     * @param string $tableName Table name (accepts with or without db prefix and _lang suffix)
     * @param string $locale IETF language tag
     *
     * @return EntityTranslatorInterface
     */
    public function buildFromTableName(string $tableName, string $locale): EntityTranslatorInterface
    {
        $dataLang = $this->dataLangFactory->buildFromTableName($tableName, $locale);

        return $this->build($dataLang);
    }

    /**
     * Builds an entity translator
     *
     * @param DataLangCore $dataLang DataLang class for this entity
     *
     * @return EntityTranslatorInterface
     */
    public function build(DataLangCore $dataLang): EntityTranslatorInterface
    {
        $selfTranslator = ($dataLang instanceof TabLang)
            ? TabTranslator::class
            : EntityTranslator::class;

        return new $selfTranslator(
            $this->db,
            $this->translator,
            $dataLang
        );
    }
}
