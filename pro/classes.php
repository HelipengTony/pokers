<?php require 'pro_header.php'; ?>

<script>
if (cookie.get('eugrade_lang') == 'zh_cn') {
        var lang_json = {
            title: {
                1: '班级',
                2: '查看/管理全部已加入班级'
            },
            tab: {
                1: '班级列表',
                2: '加入新班级',
                3: '建立新班级',
                4: '位成员'
            },
            view: {
                1: '设置',
                2: '编辑信息',
                3: '离开班级',
                4: '移除星标',
                5: '星标班级',
                6: '邀请成员',
                7: '是管理员',
                8: '账户',
                9: '邀请码',
                10: '分享你的邀请码',
                11: '其他用户可以使用此邀请码加入你的班级',
                12: '编辑班级',
                13: '编辑信息',
                14: '班级描述',
                15: '班级名',
                16: '上传'
            }
        }
    } else {
        var lang_json = {
            title: {
                1: 'Classes',
                2: 'All the classes you joined'
            },
            tab: {
                1: 'Classes',
                2: 'Join a new Class',
                3: 'Create a new Class',
                4: 'Members'
            },
            view: {
                1: 'Settings',
                2: 'Edit Info',
                3: 'Leave the class',
                4: 'Remove the mark',
                5: 'Mark this class',
                6: 'Invite People',
                7: 'is the admin',
                8: 'Account',
                9: 'Invite Code',
                10: 'Share your class’s invite ID code',
                11: 'People can use this code to join your Pokers class. Copy and share!',
                12: 'Edit Class',
                13: 'Edit Info',
                14: 'Class Description',
                15: 'Class Name',
                16: 'Upload'
            }
        }
    }
</script>


<div class="main-container" id="main-container" style="opacity:0">

    <div class="left">
        <a-spin :spinning="spinning.left">
            <div class="main-header">
                <h3>{{ lang.title[1] }}</h3>
                <p>{{ lang.title[2].substr(1,lang.title[2].length) }}</p>
            </div>
            <template v-if="Object.keys(user.joined_classes).length">
            <div class="mes-item">
                    <p>
                        <a-icon type="team"></a-icon>&nbsp;&nbsp;{{ lang.tab[1] }}
                        <a-button size="small" @click="reverse_order('classes')" style="font-size:14px;"><a-icon type="sort-descending" /></a-button>
                    </p>
                </div>
                <div v-for="(joined,index) in user.joined_classes" :class="'class-item ' + class_super(index)" @click="open_class_info(index)" :id="'class'+index">
                    <div style="margin-right: 10px;">
                        <template v-if="!!user.classes_info[index].img">
                            <img :src="user.classes_info[index].img" class="class-item-img" />
                        </template>
                        <template v-else>
                            <div class="class-img-default">
                                <p>{{ user.classes_info[index].name.substring(0,1) }}</p>
                            </div>
                        </template>
                    </div>
                    <div>
                        <h3 v-html="user.classes_info[index].name+'<em>'+user.classes_info[index].member.split(',').length+'</em>'"></h3>
                        <p v-html="user.classes_info[index].des"></p>
                    </div>
                </div>
            </template>
            <div class="class-item" @click="join_class()">
                <p>
                    <a-icon type="plus-square"></a-icon>&nbsp;&nbsp;{{ lang.tab[2] }}
                </p>
            </div>
            <?php if ($type == 2) { ?>
                <div class="class-item" @click="add_class()">
                    <p>
                        <a-icon type="plus-square"></a-icon>&nbsp;&nbsp;{{ lang.tab[3] }}
                    </p>
                </div>
            <?php } ?>
        </a-spin>
    </div>

    <div class="center class-center">
        <a-spin :spinning="spinning.center">
            <template v-if="opened_class_info.status">
                <div class="class-info-header">
                    <div style="margin-right: 12px;">
                        <template v-if="!!opened_class_info.img">
                            <img :src="opened_class_info.img" class="class-item-img class-info-img" />
                        </template>
                        <template v-else>
                            <div class="class-img-default class-info-img">
                                <p>{{ opened_class_info.name.substring(0,1) }}</p>
                            </div>
                        </template>
                    </div>
                    <div class="class-info-info">
                        <h2 v-html="opened_class_info.name"></h2>
                        <p>{{ opened_class_info.members.length }} {{ lang.tab[4] }}</p>
                    </div>
                    <div class="class-settings">
                        <a-dropdown placement="bottomRight">
                            <a-button>{{ lang.view[1] }}</a-button>
                            <a-menu slot="overlay">
                                <template v-if="user.id == opened_class_info.superid">
                                    <a-menu-item>
                                        <a @click="edit.class.visible = true">
                                            <a-icon type="edit"></a-icon> {{ lang.view[2] }}
                                        </a>
                                    </a-menu-item>
                                </template>
                                <template v-else>
                                    <a-menu-item>
                                        <a @click="stu_remove(opened_class_info.id)">
                                            <a-icon type="logout"></a-icon> {{ lang.view[3] }}
                                        </a>
                                    </a-menu-item>
                                </template>
                                <template v-if="class_marked">
                                    <a-menu-item>
                                        <a style="color:#FF4040" @click="demark_process(opened_class_info.id,'class')">
                                            <a-icon type="delete"></a-icon> {{ lang.view[4] }}
                                        </a>
                                    </a-menu-item>
                                </template>
                                <template v-else>
                                    <a-menu-item>
                                        <a style="color:#FFC125" @click="mark_process(opened_class_info.id,'class')">
                                            <a-icon type="star"></a-icon> {{ lang.view[5] }}
                                        </a>
                                    </a-menu-item>
                                </template>
                            </a-menu>
                        </a-dropdown>
                    </div>
                </div>
                <div class="class-info-des">
                    <p v-html="opened_class_info.des"></p>
                </div>
                <div class="class-invite" @click="handle_invite(opened_class_info.id)">
                    <a>
                        <a-icon type="plus-circle"></a-icon>&nbsp;&nbsp;{{ lang.view[6] }}
                    </a>
                </div>
                <div class="class-info-admin">
                    <p>{{ opened_class_info.supername }} {{ lang.view[7] }}</p>
                </div>
                <div>
                    <div v-for="(member,index_info) in opened_class_info.members" class="class-item class-info-member" @click="open_member_info(member.id)" :id="'member'+member.id">
                        <div style="margin-right: 15px;">
                            <template v-if="!!member.avatar">
                                <img :src="member.avatar" class="class-item-img" />
                            </template>
                            <template v-else>
                                <div class="class-img-default">
                                    <p>{{ member.name.substring(0,1) }}</p>
                                </div>
                            </template>
                        </div>
                        <div style="width:100%">
                            <h3 v-html="member.name"></h3>
                            <p v-html="get_level(member.type)"></p>
                        </div>
                        <div class="class-info-member-icon">
                            <a-icon type="right-circle" />
                        </div>
                    </div>
                </div>
            </template>
        </a-spin>
        <!-- 占位 -->
        <template v-if="!spinning.center && !opened_class_info.status">
            <div style="margin-top:-10px">
                <a-skeleton :paragraph="{rows: 2}" v-for="i in 6"></a-skeleton>
            </div>
        </template>
        <!-- 占位 -->
    </div>

    <div class="right">
        <a-spin :spinning="spinning.right">
            <template v-if="opened_member_info.status">
                <div class="class-info-header class-member-header">
                    <div style="margin-right: 20px;">
                        <template v-if="!!opened_member_info.info.avatar">
                            <img :src="opened_member_info.info.avatar" class="class-item-img class-member-img" />
                        </template>
                        <template v-else>
                            <div class="class-img-default class-member-img">
                                <p>{{ opened_member_info.info.name.substring(0,1) }}</p>
                            </div>
                        </template>
                    </div>
                    <div class="class-info-info class-member-info" style="padding-top: 5px;">
                        <h2 v-html="opened_member_info.info.name"></h2>
                        <p>{{ get_level(opened_member_info.info.type) }} {{ lang.view[8] }}</p>
                    </div>
                    <div class="class-member-subscribe">
                        <!-- 只允许 opened_member 对应的 super 删除账户,super 不可删除自己 -->
                        <template v-if="(parseInt(opened_member_info.superid) == parseInt(user.id)) && (parseInt(user.id) !== parseInt(opened_member_info.info.id))">
                            <a-button type="danger" @click="tea_remove(opened_member_info.classid,opened_member_info.info.id)">
                                <a-icon type="delete"></a-icon>
                            </a-button>
                        </template>
                        <template v-if="(parseInt(opened_member_info.superid) !== parseInt(user.id)) && (parseInt(user.id) == parseInt(opened_member_info.info.id))">
                            <a-button type="default" @click="edit.user.visible = true">
                                <a-icon type="edit"></a-icon>
                            </a-button>
                        </template>
                        <template v-if="member_marked">
                            <a-button type="default" @click="demark_process(opened_member_info.info.id,'user')">
                                <a-icon type="star" style="color:#FFC125"></a-icon>
                            </a-button>
                        </template>
                        <template v-else>
                            <a-button type="default" @click="mark_process(opened_member_info.info.id,'user')">
                                <a-icon type="star"></a-icon>
                            </a-button>
                        </template>
                    </div>
                </div>
                <div class="class-member-content">
                    <a-button-group>
                        <a-button>
                            <a-icon type="heat-map"></a-icon>ID: jinitaimei{{ opened_member_info.info.id }}
                        </a-button>
                        <a-button>
                            <a-icon type="flag"></a-icon> Joined on: {{ get_date(opened_member_info.info.date) }}
                        </a-button>
                    </a-button-group>
                    <br /><br />
                    <a-button-group>
                        <a-button>
                            <a-icon type="mail"></a-icon> Email: {{ opened_member_info.info.email }}
                        </a-button>
                        <a-button type="primary"><a :href="'mailto:'+opened_member_info.info.email">Mail To</a></a-button>
                    </a-button-group>
                    <br /><br />
                    <a-button-group>
                        <a-button>
                            <a-icon type="team"></a-icon> Joined {{ opened_member_info.info.class.split(',').length }} Class
                        </a-button>
                    </a-button-group>
                </div>
            </template>
        </a-spin>
        <!-- 占位 -->
        <template v-if="!spinning.right && !opened_member_info.status">
            <div style="padding:20px 30px">
                <a-skeleton avatar :paragraph="{rows: 1}" v-for="i in 9"></a-skeleton>
            </div>
        </template>
        <!-- 占位 -->
    </div>



</div>



<?php if ((int)$type == 2) { ?>
    <!-- 新建班级 -->
    <a-modal :title="lang.tab[3]" :visible="add.visible" @ok="handle_create_submit" :confirm-loading="add.confirm_create_loading" @cancel="handle_create_cancel">
        <a-input :placeholder="lang.view[15]" v-model="add.class.name">
            <a-icon slot="prefix" type="team" />
        </a-input>
        <br /><br />
        <a-textarea :placeholder="lang.view[14]" v-model="add.class.des" :rows="4" />
    </a-modal>
    <!-- 新建班级结束 -->
    <!-- 班级信息修改 -->
    <a-modal :title="lang.view[12]" :visible="edit.class.visible" @ok="handle_edit_class_submit" :confirm-loading="edit.confirm_edit_class_loading" @cancel="handle_edit_class_cancel">
        <div>
            <template v-if="!!opened_class_info.img">
                <a-avatar size="large" :src="opened_class_info.img"></a-avatar>
            </template>
            <template v-else>
                <a-avatar size="large" :style="{backgroundColor: '#32a3bf', verticalAlign: 'middle'}">{{ opened_class_info.name }}</a-avatar>
            </template>
            <input type="file" name="class_img" id="class_img" />
            <a-button size="small" :style="{ marginLeft: 16, verticalAlign: 'middle' }" @click="upload_class_img('<?php echo $upToken; ?>')">{{ lang.view[16] }}</a-button>
        </div>
        <div v-show="edit.class.display_percent">
            <a-progress :percent="edit.class.percent" status="active"></a-progress>
            <br />
        </div>
        <br />
        <a-input :placeholder="lang.view[15]" v-model="edit.class.name">
            <a-icon slot="prefix" type="team" />
        </a-input>
        <br /><br />
        <a-textarea :placeholder="lang.view[14]" v-model="edit.class.des" :rows="4" />
    </a-modal>
    <!-- 班级信息修改结束 -->
    <!-- 用户信息修改 -->
    <a-modal :title="lang.view[13]" :visible="edit.user.visible" @ok="handle_edit_user_submit('teacher')" :confirm-loading="edit.confirm_edit_user_loading" @cancel="handle_edit_user_cancel">
        <div>
            <template v-if="!!edit.user.avatar">
                <a-avatar size="large" :src="edit.user.avatar"></a-avatar>
            </template>
            <template v-else>
                <a-avatar size="large" :style="{backgroundColor: '#32a3bf', verticalAlign: 'middle'}">{{ edit.user.name }}</a-avatar>
            </template>
            <input type="file" name="user_img" id="user_img" />
            <a-button size="small" :style="{ marginLeft: 16, verticalAlign: 'middle' }" @click="upload_user_img('<?php echo $upToken; ?>')">Upload</a-button>
        </div>
        <div v-show="edit.user.display_percent">
            <a-progress :percent="edit.user.percent" status="active"></a-progress>
            <br />
        </div>
        <br />
        <a-input placeholder="Nickname" v-model="edit.user.name">
            <a-icon slot="prefix" type="user" />
        </a-input>
        <br /><br />
        <a-input placeholder="Email" v-model="edit.user.email">
    </a-modal>
    <!-- 用户信息修改结束 -->
<?php } else { ?>
    <!-- 用户信息修改 -->
    <a-modal :title="lang.view[13]" :visible="edit.user.visible" @ok="handle_edit_user_submit('user')" :confirm-loading="edit.confirm_edit_user_loading" @cancel="handle_edit_user_cancel">
        <div>
            <template v-if="!!edit.user.avatar">
                <a-avatar size="large" :src="edit.user.avatar"></a-avatar>
            </template>
            <template v-else>
                <a-avatar size="large" :style="{backgroundColor: '#32a3bf', verticalAlign: 'middle'}">{{ edit.user.name }}</a-avatar>
            </template>
            <input type="file" name="user_img" id="user_img" />
            <a-button size="small" :style="{ marginLeft: 16, verticalAlign: 'middle' }" @click="upload_user_img('<?php echo $upToken; ?>')">Upload</a-button>
        </div>
        <div v-show="edit.user.display_percent">
            <a-progress :percent="edit.user.percent" status="active"></a-progress>
            <br />
        </div>
        <br />
        <a-input placeholder="Nickname" v-model="edit.user.name">
            <a-icon slot="prefix" type="user" />
        </a-input>
        <br /><br />
        <a-input placeholder="Email" v-model="edit.user.email">
            <a-icon slot="prefix" type="mail" />
        </a-input>
        <br /><br />
        <a-input placeholder="Password(stays the same if kept empty)" v-model="edit.user.pwd">
            <a-icon slot="prefix" type="key" />
        </a-input>
    </a-modal>
    <!-- 用户信息修改结束 -->
<?php } ?>

<!-- 加入班级 -->
<a-modal :title="lang.tab[2]" :visible="join.visible" @ok="handle_join_submit" :confirm-loading="join.confirm_join_loading" @cancel="handle_join_cancel">
        <a-input placeholder="Class ID" v-model="join.id">
            <a-icon slot="prefix" type="team" />
        </a-input>
    </a-modal>
    <!-- 加入班级结束 -->

<a-modal :footer="null" :title="lang.view[9]" centered v-model="invite.visible" @cancel="handle_invite_close">
    <div class="class-invite-div">
        <h2>{{ lang.view[10] }}</h2>
        <p>{{ lang.view[11] }}</p>
    </div>
    <p class="class-invite-code">
        <a-tag color="#2db7f5">{{ invite.id }}</a-tag>
    </p>
</a-modal>












</div>
<script type="text/javascript" src="../dist/js/classes.js"></script>


<?php require 'pro_footer.php'; ?>