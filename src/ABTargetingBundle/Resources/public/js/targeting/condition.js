(function () {
    'use strict';

    pimcore.settings.targeting.conditions.register(
        'abcondition',
        Class.create(pimcore.settings.targeting.condition.abstract, {
            getName: function () {
                return t("ABCondition");
            },

            getPanel: function (panel, data) {
                var id = Ext.id();

                return new Ext.form.FormPanel({
                    id: id,
                    forceLayout: true,
                    style: 'margin: 10px 0 0 0',
                    bodyStyle: 'padding: 10px 30px 10px 30px; min-height:40px;',
                    tbar: pimcore.settings.targeting.conditions.getTopBar(this, id, panel, data),
                    items: [
                        {
                            xtype: "combo",
                            multiSelect: true,
                            fieldLabel: t('not_in_target_group'),
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
                            xtype: 'hidden',
                            name: 'type',
                            value: 'abcondition' // the identifier chosen before when registering the PHP class
                        }
                    ]
                });
            }
        })
    );
}());
