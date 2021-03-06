<?php
/*
 * This file is part of AtroPIM.
 *
 * AtroPIM - Open Source PIM application.
 * Copyright (C) 2020 AtroCore UG (haftungsbeschränkt).
 * Website: https://atropim.com
 *
 * AtroPIM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * AtroPIM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AtroPIM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "AtroPIM" word.
 */

declare(strict_types=1);

namespace Pim\Listeners;

use Espo\Core\Exceptions\BadRequest;
use Pim\Repositories\Category;
use Treo\Core\EventManager\Event;

/**
 * Class CatalogEntity
 */
class CatalogEntity extends AbstractEntityListener
{
    /**
     * Before save
     *
     * @param Event $event
     *
     * @throws BadRequest
     */
    public function beforeSave(Event $event)
    {
        if (!$this->isCodeValid($event->getArgument('entity'))) {
            throw new BadRequest(
                $this->translate('codeIsInvalid', 'exceptions', 'Global')
            );
        }
    }

    /**
     * @param Event $event
     *
     * @throws BadRequest
     */
    public function beforeRelate(Event $event)
    {
        if ($event->getArgument('relationName') == 'categories'
            && !empty($foreign = $event->getArgument('foreign'))
            && !is_string($foreign)
            && !empty($foreign->get('categoryParent'))) {
            throw new BadRequest($this->exception('Only root category can be linked with catalog'));
        }
    }

    /**
     * @param Event $event
     *
     * @throws BadRequest
     */
    public function beforeUnrelate(Event $event)
    {
        if ($event->getArgument('relationName') == 'categories') {
            $this->getCategoryRepository()->canUnRelateCatalog($event->getArgument('foreign'), $event->getArgument('entity'));
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function exception(string $key): string
    {
        return $this->translate($key, 'exceptions', 'Catalog');
    }

    /**
     * @return Category
     */
    protected function getCategoryRepository(): Category
    {
        return $this->getEntityManager()->getRepository('Category');
    }
}
