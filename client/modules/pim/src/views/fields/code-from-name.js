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

Espo.define('pim:views/fields/code-from-name', 'pim:views/fields/varchar-with-pattern',
    Dep => Dep.extend({

        validationPattern: '^[a-z_0-9{}]+$',

        getPatternValidationMessage() {
            return this.translate('fieldHasPattern', 'messages').replace('{field}', this.translate(this.name, 'fields', this.model.name));
        },

        setup() {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:name', () => {
                if (!this.model.get('code')) {
                    let value = this.model.get('name');
                    if (value) {
                        this.model.set({[this.name]: this.transformToPattern(value)});
                    }
                }
            });
        },

        transformToPattern(value) {
            let replacementSymbols = {
                    \u00e4: 'ae',
                    \u00fc: 'ue',
                    \u00df: 'ss',
                    \u00f6: 'oe',
                    \u00d6: 'oe',
                    \u00e5: 'a'
                };

            return value
                .toLowerCase()
                .replace(/ /g, '_')
                .replace(new RegExp('[' + Object.keys(replacementSymbols).join('') + ']', 'gu'), function (str) {
                    return replacementSymbols[str];
                })
                .replace(/[^a-z_0-9]/gu, '');
        }
    })
);
