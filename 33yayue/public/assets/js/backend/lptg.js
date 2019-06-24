define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lptg/index',
                    add_url: 'lptg/add',
                    edit_url: 'lptg/edit',
                    del_url: 'lptg/del',
                    multi_url: 'lptg/multi',
                    table: 'lptg',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'ID',
                sortName: 'ID',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('Id')},
                        {field: 'KP_SiteID', title: __('Kp_siteid')},
                        {field: 'KP_LpID', title: __('Kp_lpid')},
                        {field: 'KP_Title', title: __('Kp_title')},
                        {field: 'KP_Yhby', title: __('Kp_yhby')},
                        {field: 'KP_Yhby2', title: __('Kp_yhby2')},
                        {field: 'KP_Yhxq', title: __('Kp_yhxq')},
                        {field: 'KP_EndTime', title: __('Kp_endtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'KP_Htnr', title: __('Kp_htnr')},
                        {field: 'KP_Htzt', title: __('Kp_htzt')},
                        {field: 'KP_PicUrl', title: __('Kp_picurl'), formatter: Table.api.formatter.url},
                        {field: 'KP_PicUrl2', title: __('Kp_picurl2')},
                        {field: 'KP_AddUser', title: __('Kp_adduser')},
                        {field: 'KP_AddTime', title: __('Kp_addtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'KP_EditUser', title: __('Kp_edituser')},
                        {field: 'KP_EditTime', title: __('Kp_edittime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'KP_Content', title: __('Kp_content')},
                        {field: 'lp.id', title: __('Lp.id')},
                        {field: 'lp.KP_LpName', title: __('Lp.kp_lpname')},
                        {field: 'lp.KP_Lppinyin', title: __('Lp.kp_lppinyin')},
                        {field: 'lp.KP_Lppinyins', title: __('Lp.kp_lppinyins')},
                        {field: 'lp.KP_City', title: __('Lp.kp_city')},
                        {field: 'lp.KP_TsType', title: __('Lp.kp_tstype')},
                        {field: 'lp.KP_Wylx', title: __('Lp.kp_wylx')},
                        {field: 'lp.KP_Jzlx', title: __('Lp.kp_jzlx')},
                        {field: 'lp.KP_Wjt', title: __('Lp.kp_wjt')},
                        {field: 'lp.KP_Zxqk', title: __('Lp.kp_zxqk')},
                        {field: 'lp.KP_Wyf', title: __('Lp.kp_wyf')},
                        {field: 'lp.KP_Kfs', title: __('Lp.kp_kfs')},
                        {field: 'lp.KP_Ysxkz', title: __('Lp.kp_ysxkz')},
                        {field: 'lp.KP_Cqnx', title: __('Lp.kp_cqnx')},
                        {field: 'lp.KP_Lpdz', title: __('Lp.kp_lpdz')},
                        {field: 'lp.KP_Sldz', title: __('Lp.kp_sldz')},
                        {field: 'lp.KP_Tel', title: __('Lp.kp_tel')},
                        {field: 'lp.KP_Fjh', title: __('Lp.kp_fjh')},
                        {field: 'lp.KP_KpTime', title: __('Lp.kp_kptime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'lp.KP_RzTime', title: __('Lp.kp_rztime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'lp.KP_Rjl', title: __('Lp.kp_rjl')},
                        {field: 'lp.KP_Lhl', title: __('Lp.kp_lhl')},
                        {field: 'lp.KP_Ghhs', title: __('Lp.kp_ghhs')},
                        {field: 'lp.KP_Xszt', title: __('Lp.kp_xszt')},
                        {field: 'lp.KP_Tj', title: __('Lp.kp_tj')},
                        {field: 'lp.KP_Cs', title: __('Lp.kp_cs')},
                        {field: 'lp.KP_Jz', title: __('Lp.kp_jz')},
                        {field: 'lp.KP_Mapzb', title: __('Lp.kp_mapzb')},
                        {field: 'lp.KP_Yhz', title: __('Lp.kp_yhz')},
                        {field: 'lp.KP_Parking', title: __('Lp.kp_parking')},
                        {field: 'lp.KP_TaoJia', title: __('Lp.kp_taojia')},
                        {field: 'lp.KP_YouHui', title: __('Lp.kp_youhui')},
                        {field: 'lp.KP_Zlhx', title: __('Lp.kp_zlhx')},
                        {field: 'lp.KP_Logo', title: __('Lp.kp_logo')},
                        {field: 'lp.KP_Wygs', title: __('Lp.kp_wygs')},
                        {field: 'lp.KP_Gjlx', title: __('Lp.kp_gjlx')},
                        {field: 'lp.KP_Zds', title: __('Lp.kp_zds')},
                        {field: 'lp.KP_Gharea', title: __('Lp.kp_gharea')},
                        {field: 'lp.KP_Jzarea', title: __('Lp.kp_jzarea')},
                        {field: 'lp.KP_Zycs', title: __('Lp.kp_zycs')},
                        {field: 'lp.KP_Jjlp', title: __('Lp.kp_jjlp')},
                        {field: 'lp.KP_Gflm', title: __('Lp.kp_gflm')},
                        {field: 'lp.KP_AddUser', title: __('Lp.kp_adduser')},
                        {field: 'lp.KP_AddTime', title: __('Lp.kp_addtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'lp.KP_EditUser', title: __('Lp.kp_edituser')},
                        {field: 'lp.KP_EditTime', title: __('Lp.kp_edittime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'lp.KP_Content', title: __('Lp.kp_content')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});