(function () {
    'use strict';

    pimcore.settings.targeting.actions.register(
        'abactionhandler',
        Class.create(pimcore.settings.targeting.action.abstract, {
            getName: function () {
                return t('ABActionHandler');
            },

            getPanel: function (panel, data) {
                var id = Ext.id();

                return new Ext.form.FormPanel({
                    id: id,
                    forceLayout: true,
                    style: 'margin: 10px 0 0 0',
                    bodyStyle: 'padding: 10px 30px 10px 30px; min-height:40px;',
                    tbar: pimcore.settings.targeting.actions.getTopBar(this, id, panel),
                    items: [
                        {
                            xtype: "combo",
                            multiSelect: true,
                            fieldLabel: t('target_group'),
                            name: "targetGroups",
                            displayField: 'text',
                            valueField: "id",
                            store: pimcore.globalmanager.get("target_group_store"),
                            editable: false,
                            width: 400,
                            triggerAction: 'all',
                            listWidth: 200,
                            mode: "local",
                            value: data.targetGroups,
                            emptyText: t("select_a_target_group")
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: t('assign_target_group_weight'),
                            name: "weight",
                            value: data.weight ? data.weight : 1,
                            width: 200,
                            minValue: 1,
                            allowDecimals: false
                        },
                        {
                            xtype: 'hidden',
                            name: 'type',
                            value: 'abactionhandler'
                        }
                    ]
                });
            }
        })
    );
}());