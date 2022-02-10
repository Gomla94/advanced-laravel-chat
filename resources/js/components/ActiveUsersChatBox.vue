<template>
    <div class="activeUsersBox">
        <div class="chat-window col-xs-5 col-md-3 p-0 chat-wrapper">
            <div class="col-xs-12 col-md-12 p-0">
                <div class="panel panel-default">
                    <div class="panel-heading active-users-top-bar">
                        <div class="col-md-8 col-xs-8">
                            <h3 class="panel-title">
                                <span
                                    class="glyphicon glyphicon-comment"
                                ></span>
                                Chat
                            </h3>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <a href="#" style="color: black"
                                ><span
                                    id="minim_chat_window"
                                    class="fas fa-chevron-down"
                                    @click="minimize('active-users-panel-body')"
                                ></span
                            ></a>
                        </div>
                    </div>
                    <ul
                        class="panel-body msg_container_base active-users-panel-body"
                    >
                        <div class="text-center p-2">
                            <strong style="color: black">Joined Groups!</strong>
                        </div>
                        <div>
                            <ul
                                class="chat-users-list"
                                style="height: auto; overflow-y: scroll; list-style: none"
                            >
                                <li
                                    v-for="(group, index) in groups"
                                    :key="index"
                                >
                                    <a
                                        href="#chat"
                                        @click="
                                            newChat(
                                                `group-${group.name}`,
                                                undefined,
                                                group.id
                                            )
                                        "
                                        >{{ group.name }}</a
                                    >
                                </li>
                            </ul>
                        </div>

                        <div class="text-center p-2">
                            <strong>Currently Active Users!</strong>
                        </div>
                        <div>
                            <ul
                                class="chat-users-list"
                                style="height: 450px; overflow-y: scroll; list-style: none"
                            >
                                <li v-for="(user, index) in users" :key="index">
                                    <a
                                        style="display: flex; margin-bottom: 10px"
                                        href="#chat"
                                        @click="
                                            newChat(
                                                user.name,
                                                user.id,
                                                null,
                                                user.image
                                            )
                                        "
                                    >
                                        <img
                                            :src="'/images/' + user.image"
                                            v-if="user.image"
                                            alt=""
                                            style="width: 30px; margin-right: 10px"
                                        />
                                        <img
                                            src="/images/avatar.png"
                                            v-else
                                            alt=""
                                            style="width: 30px; margin-right: 10px"
                                        />

                                        {{ user.name }}</a
                                    >
                                </li>
                            </ul>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
        <div style="cursor: pointer">
            <div class="activeUsersBtn" @click="toggleChatWrapper">
                <i
                    class="far fa-edit"
                    style="font-size: 20px; margin-left: 17px"
                ></i>
            </div>
        </div>
    </div>
</template>

<script>
import NewChatBox from "./NewChatBoxComponent.vue";

export default {
    props: ["authuser", "divID", "usergroups"],
    components: {
        NewChatBox
    },
    mounted() {
        if (sessionStorage.getItem("usersOpenedChat")) {
            this.usersOpenedChatArray = JSON.parse(
                sessionStorage.getItem("usersOpenedChat")
            );

            //when the component is mounted loop through the usersOpenedChatArray, and for every user object in the array
            //run the newChat function that is responsable for openeing the chat windows, so by doing this whenever i refresh
            //the page if there was any opened chat it will be automatically opened.
            this.usersOpenedChatArray.forEach(el => {
                this.newChat(el.name, el.id, el.groupid, el.userimage);
            });
        }

        // window.Echo.channel("joinChat-channel").listen(
        //   "JoinChatChannelEvent",
        //   (event) => {
        //     this.users.push(event.user);
        //   }
        // );

        // console.log(this.usergroups);

        this.groups = this.usergroups;
        this.groups.forEach(group => {
            window.Echo.private(`group.${group.id}`).listen(
                "MessageSentEvent",
                event => {
                    // console.log(event);
                    this.messages.push(event.message);
                    this.newChat(
                        `group-${group.name}`,
                        undefined,
                        event.message.group_id
                    );
                }
            );
        });
    },
    data() {
        return {
            messages: [],
            newMessage: "",
            users: [],
            activeUser: false,
            typingTimer: false,
            opacityClass: false,
            alreadyExistedDiv: null,
            usersOpenedChatArray: [],
            groups: [],
            chatContainerToggledRight: false
        };
    },
    created() {
        //if there is no session created then create one and put the usersOpenedChatArray in it,
        //because we are gonna use it to automatically open the previously opened chat windows.

        // console.log(this.authuser.id);

        if (!sessionStorage.getItem("usersOpenedChat")) {
            sessionStorage.setItem(
                "usersOpenedChat",
                JSON.stringify(this.usersOpenedChatArray)
            );
        }

        window.Echo.join("joinChat-channel")
            //here i am taking the logged in user and i am joining the unique private channel,
            //and i am passing the authenticated user id with other authenticated users id's,
            //then listen to the new message event then run the newChat function.

            .here(user => {
                user.forEach(user => {
                    // console.log(user.image);
                    if (user.id !== this.authuser.id) {
                        this.users.push(user);
                        window.Echo.private(
                            `messages.${user.id}.${this.authuser.id}`
                        ).listen("MessageSentEvent", event => {
                            // console.log("hehehehe advancedchat");
                            this.newChat(
                                user.name,
                                user.id,
                                event.message.group_id,
                                user.image
                            );
                        });
                    }
                });
            })

            .joining(user => {
                // console.log("joined", user.image);
                //if a user was newly logged in then join the unique private channel, and listen to the new message event,
                //then run the newChat function.
                console.log("joined");
                window.Echo.private(
                    `messages.${user.id}.${this.authuser.id}`
                ).listen("MessageSentEvent", event => {
                    // console.log("hehehehe advancedchat");
                    this.newChat(
                        user.name,
                        user.id,
                        event.message.group_id,
                        user.image
                    );
                });
                this.users.push(user);
            })
            .leaving(user => {
                //when a user logged out or left the web site then filter the users array,
                //the users array is automatically listed in the active users box.
                console.log("left");
                this.users = this.users.filter(u => {
                    return user.id != u.id;
                });
            });

        window.Echo.private(`user.${this.authuser.id}`).listen(
            "GroupCreatedEvent",
            event => {
                // console.log("group created");
                this.groups.push(event.group);
            }
        );
    },

    methods: {
        toggleOpacity() {
            this.opacityClass = !this.opacityClass;
        },

        minimize(value) {
            //this message is responsible for minimizing the chat box.
            console.log(value);
            const expandSpan = event.target;
            if (!expandSpan.classList.contains("panel-collapsed")) {
                $(`.active-users-panel-body`).slideUp();
                expandSpan.classList.add("panel-collapsed");
                // document.querySelector(`.panel-footer`).style.display = "none";
                expandSpan.classList.remove("fa-minus");
                expandSpan.classList.add("fa-chevron-up");
            } else {
                $(`.active-users-panel-body`).slideDown();
                expandSpan.classList.remove("panel-collapsed");
                document.querySelector(`.${value}`).style.display = "block";
                expandSpan.classList.remove("fa-chevron-up");
                expandSpan.classList.add("fa-chevron-down");
            }
        },

        toggleChatWrapper() {
            const chatWrapper = document.querySelector(".chat-wrapper");
            const activeUsersBtn = document.querySelector(".activeUsersBtn");
            const chatContainer = document.querySelector(".chat-container");
            chatContainer.classList.toggle("toggleChatContainer");
            // if (this.chatContainerToggledRight) {
            //   this.chatContainerToggledRight = false;
            //   chatContainer.style.paddingRight = "190px";
            // } else {
            //   this.chatContainerToggledRight = true;
            //   chatContainer.style.paddingRight = "80px";
            // }
            activeUsersBtn.classList.toggle("toggleActiveUsersBtnRight");
            chatWrapper.classList.toggle("toggleDisplay");
        },

        newChat(name, id, groupid, userimage) {
            // console.log(name, id, groupid, userimage);
            this.usersOpenedChatArray = JSON.parse(
                sessionStorage.getItem("usersOpenedChat")
            );

            //if window chat is opened with this user then do not put a new object in the usersOpenedChatArray,
            //this is why i am checking the usersOpenedChatArray.
            if (
                !this.usersOpenedChatArray.find(
                    userChat => userChat.name == name
                )
            ) {
                let userChat = { id, name, groupid, userimage };
                this.usersOpenedChatArray.push(userChat);
                sessionStorage.setItem(
                    `usersOpenedChat`,
                    JSON.stringify(this.usersOpenedChatArray)
                );
            }
            const componentContainers = document.querySelectorAll(
                ".componentContainer"
            );

            console.log("newChat");
            // return false;
            const alreadyExistedDiv = id
                ? document.getElementById(`componentContainer${id}`)
                : document.getElementById(`componentContainerGroup${groupid}`);

            //if there is a chat window existed with this user id then return false.
            if (alreadyExistedDiv) return false;

            //if there are 3 opened chat windows and i wanna open a fourth one,
            //then remove the first one (MAXIMUM NUMBER OF 3 OPENED CHAT WINDOW)
            if (componentContainers.length == 3) {
                componentContainers[2].remove();
            }

            //creating  new div element and append the NewChatBox component in it with some props will be needed in the other component,
            //then append the new div in the chatContainer element.

            const chatContainer = document.querySelector(".chat-container");
            const componentContainer = document.createElement("div");
            let componentContainerId;
            componentContainer.classList.add("componentContainer", "chatBox");
            if (id !== undefined) {
                componentContainerId = `componentContainer${id}`;
                componentContainer.setAttribute("id", componentContainerId);
            } else {
                componentContainerId = `componentContainerGroup${groupid}`;
                componentContainer.setAttribute("id", componentContainerId);
            }

            let vuecomp = Vue.extend(NewChatBox);
            let comp = new vuecomp({
                propsData: {
                    fromUserID: this.authuser.id,
                    toUserID: id,
                    panelFooter:
                        id !== undefined
                            ? `panel-footer-${id}`
                            : `panel-footer-group-${groupid}`,
                    userName: name,
                    usersOpenedChatArray: this.usersOpenedChatArray,
                    componentID: componentContainerId,
                    groupID: groupid,
                    authuser: this.authuser,
                    userimage: userimage ? userimage : null
                }
            }).$mount(componentContainer);
            componentContainer.append(comp.$el);
            chatContainer.prepend(componentContainer);
        }
    }
};
</script>

<style scoped>
.col-md-2,
.col-md-10 {
    padding: 0;
}
.col-xs-4 {
    left: 30px;
}
.panel {
    margin-bottom: 0px;
}

.chat-container {
    position: fixed;
    /* right: 20px; */
    padding-right: 340px;
    bottom: 0px;
    width: 100%;
    z-index: 10;
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
}

.toggleChatContainer {
    padding-right: 120px !important;
}

.panel-footer {
    padding: 10px 15px;
    background-color: #f5f5f5;
    border-top: 1px solid #ddd;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
}

.activeUsersBtn {
    height: 50px;
    width: 55px;
    background-color: white;
    margin-right: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    box-shadow: 0px 7px 30px silver;
}

.toggleDisplay {
    display: none;
}

.toggleActiveUsersBtnRight {
    position: fixed;
    bottom: 15px;
}

.panel-title {
    font-size: 16px;
    margin-bottom: 0;
}

.hideBtn {
    display: none;
}

.btn-chat {
    height: 100%;
}
.chat-window {
    bottom: 0;
    position: fixed;
    /* float:right;
    margin-left:10px; */
    margin-right: 2px;
    width: 300px;
    right: 20px;
    z-index: 10;
    box-shadow: 2px 2px 15px 1px grey !important;
}

.messageImage {
    height: 150px;
    width: 150px;
}

.chat-window > div > .panel {
    border-radius: 5px 5px 0 0;
}
.icon_minim {
    padding: 2px 10px;
}
.msg_container_base {
    margin: 0;
    padding: 0 10px 10px;
    overflow-x: hidden;
    list-style: none;
}
.active-users-top-bar {
    color: black;
    padding: 10px;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
}
.msg_receive {
    padding-left: 0;
    margin-left: 0;
}
.msg_sent {
    padding-bottom: 20px !important;
    margin-right: 0;
}
.messages {
    background: white;
    padding: 10px;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    max-width: 100%;
}
.messages > p {
    font-size: 13px;
    margin: 0 0 0.2rem 0;
}
.messages > time {
    font-size: 11px;
    color: #ccc;
}
.msg_container {
    padding: 10px;
    overflow: hidden;
    display: flex;
}
img {
    display: block;
    width: 100%;
}
.avatar {
    position: relative;
}
.base_receive > .avatar:after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 0;
    height: 0;
    border: 5px solid #fff;
    border-left-color: rgba(0, 0, 0, 0);
    border-bottom-color: rgba(0, 0, 0, 0);
}

.base_sent {
    justify-content: flex-end;
    align-items: flex-end;
}
.base_sent > .avatar:after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 0;
    border: 5px solid white;
    border-right-color: transparent;
    border-top-color: transparent;
    box-shadow: 1px 1px 2px rgba(black, 0.2);
}

.msg_sent > time {
    float: right;
}

.msg_container_base::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 0px rgba(0, 0, 0, 0.3);
    background-color: #f5f5f5;
}

.msg_container_base::-webkit-scrollbar {
    width: 5px;
    background-color: #f5f5f5;
}

.msg_container_base::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 0px rgba(0, 0, 0, 0.3);
    background-color: #555;
}

.btn-group.dropup {
    position: fixed;
    left: 0px;
    bottom: 0;
}

.replyMessageImage {
    margin-left: 210px;
}

@media only screen and (max-width: 1000px) {
    .col-xs-4 {
        display: contents;
    }

    .active-users-top-bar {
        color: black;
        padding: 10px;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-evenly;
        height: 70px;
    }

    .chat-container {
        position: fixed;
        /* right: 20px; */
        padding-right: 170px;
        bottom: 0px;
        width: 100%;
        z-index: 10;
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
    }

    .chat-window {
        bottom: 0;
        position: fixed;
        /* float:right;
    margin-left:10px; */
        margin-right: 2px;
        width: 180px;
    }

    .chatWindowBtns {
        margin-right: 30px;
    }

    .messageImage {
        height: 150px;
        width: 100%;
    }

    .toggleChatContainer {
        padding-right: 80px !important;
    }
}
</style>
