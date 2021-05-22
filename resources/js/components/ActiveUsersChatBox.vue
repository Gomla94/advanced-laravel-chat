<template >
  <div class="activeUsersBox">
    <div
      class="chat-wrapper"
      :class="{ toggleOpacityClass: opacityClass }"
      style="border: 1px solid grey"
    >
      <div class="header">
        <h6>Let's Chat - Online</h6>
      </div>
      <div class="text-center p-2">
        <strong style="color: black">Joined Groups!</strong>
      </div>
      <div>
        <ul class="chat-users-list" style="height: auto; overflow-y: scroll">
          <li v-for="(group, index) in groups" :key="index">
            <a
              href="#chat"
              @click="newChat(`group-${group.name}`, undefined, group.id)"
              >{{ group.name }}</a
            >
          </li>
        </ul>
      </div>
      <div class="text-center p-2">
        <strong style="color: black">Currently Active Users!</strong>
      </div>
      <div>
        <ul class="chat-users-list" style="height: 300px; overflow-y: scroll">
          <li v-for="(user, index) in users" :key="index">
            <a href="#chat" @click="newChat(user.name, user.id)">{{
              user.name
            }}</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- <div style="z-index=10;margin-bottom:15px;cursor:pointer">
      <div
        class="activeUsersBtn"
        style="
          height: 50px;
          width: 55px;
          background-color: white;
          margin-right: 100px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          box-shadow: 0px 7px 30px silver;
        "
        @click="toggleChatWrapper"
      >
        <i class="far fa-edit" style="font-size: 20px; margin-left: 17px"></i>
      </div>
    </div> -->
  </div>
</template>

<script>
import NewChatBox from "./NewChatBoxComponent.vue";

export default {
  props: ["authuser", "divID", "usergroups"],
  components: {
    NewChatBox,
  },
  mounted() {
    if (sessionStorage.getItem("usersOpenedChat")) {
      this.usersOpenedChatArray = JSON.parse(
        sessionStorage.getItem("usersOpenedChat")
      );

      //when the component is mounted loop through the usersOpenedChatArray, and for every user object in the array
      //run the newChat function that is responsable for openeing the chat windows, so by doing this whenever i refresh
      //the page if there was any opened chat it will be automatically opened.
      this.usersOpenedChatArray.forEach((el) => {
        this.newChat(el.name, el.id, el.groupid);
      });
    }

    console.log(this.usergroups);
    this.groups = this.usergroups;
    this.groups.forEach((group) => {
      window.Echo.private(`group.${group.id}`).listen(
        "MessageSentEvent",
        (event) => {
          console.log(event);
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
      chatContainerToggledRight: false,
    };
  },
  created() {
    //if there is no session created then create one and put the usersOpenedChatArray in it,
    //because we are gonna use it to automatically open the previously opened chat windows.

    console.log(this.authuser.id);
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
      .here((user) => {
        user.forEach((user) => {
          console.log(user.id, this.authuser.id);
          if (user.id !== this.authuser.id) {
            this.users.push(user);
            window.Echo.private(
              `messages.${user.id}.${this.authuser.id}`
            ).listen("MessageSentEvent", (event) => {
              console.log("hehehehe advancedchat");
              this.newChat(user.name, user.id);
            });
          }
        });
      })
      .joining((user) => {
        console.log("joined");
        //if a user was newly logged in then join the unique private channel, and listen to the new message event,
        //then run the newChat function.
        window.Echo.private(`messages.${user.id}.${this.authuser.id}`).listen(
          "MessageSentEvent",
          (event) => {
            console.log("hehehehe advancedchat");
            this.newChat(user.name, user.id);
          }
        );
        this.users.push(user);
      })
      .leaving((user) => {
        //when a user logged out or left the web site then filter the users array,
        //the users array is automatically listed in the active users box.
        this.users = this.users.filter((u) => {
          return user.id != u.id;
        });
      });

    window.Echo.private(`user.${this.authuser.id}`).listen(
      "GroupCreatedEvent",
      (event) => {
        console.log("group created");
        this.groups.push(event.group);
      }
    );
  },

  methods: {
    toggleOpacity() {
      this.opacityClass = !this.opacityClass;
    },

    toggleChatWrapper() {
      const chatWrapper = document.querySelector(".chat-wrapper");
      const activeUsersBtn = document.querySelector(".activeUsersBtn");
      const chatContainer = document.querySelector(".chat-container");
      if (this.chatContainerToggledRight) {
        this.chatContainerToggledRight = false;
        chatContainer.style.paddingRight = "190px";
      } else {
        this.chatContainerToggledRight = true;
        chatContainer.style.paddingRight = "80px";
      }
      activeUsersBtn.classList.toggle("toggleActiveUsersBtnRight");
      chatWrapper.classList.toggle("toggleDisplay");
    },

    newChat(name, id, groupid) {
      this.usersOpenedChatArray = JSON.parse(
        sessionStorage.getItem("usersOpenedChat")
      );

      //if window chat is opened with this user then do not put a new object in the usersOpenedChatArray,
      //this is why i am checking the usersOpenedChatArray.
      if (
        !this.usersOpenedChatArray.find((userChat) => userChat.name == name)
      ) {
        let userChat = { id, name, groupid };
        this.usersOpenedChatArray.push(userChat);
        sessionStorage.setItem(
          `usersOpenedChat`,
          JSON.stringify(this.usersOpenedChatArray)
        );
      }
      const componentContainers = document.querySelectorAll(
        ".componentContainer"
      );

      const alreadyExistedDiv = id
        ? document.getElementById(`componentContainer${id}`)
        : document.getElementById(`componentContainer${name}`);

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
      let componentConntainerId;
      componentContainer.classList.add("componentContainer", "chatBox");
      if (id !== undefined) {
        componentConntainerId = `componentContainer${id}`;
        componentContainer.setAttribute("id", componentConntainerId);
      } else {
        componentConntainerId = `componentContainer${name}`;
        componentContainer.setAttribute("id", componentConntainerId);
      }

      let vuecomp = Vue.extend(NewChatBox);
      let comp = new vuecomp({
        propsData: {
          fromUserID: this.authuser.id,
          toUserID: id,
          panelFooter:
            id !== undefined ? `panel-footer-${id}` : `panel-footer-${name}`,
          userName: name,
          usersOpenedChatArray: this.usersOpenedChatArray,
          componentID: componentConntainerId,
          groupID: groupid,
          authuser: this.authuser,
        },
      }).$mount(componentContainer);
      componentContainer.append(comp.$el);
      chatContainer.prepend(componentContainer);
    },
  },
};
</script>

<style scoped>
body {
  background-color: #eee;
}

::-webkit-scrollbar {
  width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey;
  border-radius: 10px;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: blue;
  border-radius: 10px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #b30000;
}

.toggleOpacityClass {
  opacity: 1 !important;
}

.toggleDisplay {
  display: none;
}

.toggleActiveUsersBtnRight {
  position: fixed;
  bottom: 15px;
}

.chat-btn {
  position: fixed;
  right: 14px;
  cursor: pointer;
  width: 50px;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50px;
  background-color: blue;
  color: #fff;
  font-size: 22px;
  border: none;
  z-index: 10;
}

.chat-btn .close {
  display: none;
}

.chat-btn i {
  transition: all 0.9s ease;
}

#check:checked ~ .chat-btn i {
  display: block;
  pointer-events: auto;
  /* transform: rotate(180deg) */
}

#check:checked ~ .chat-btn .fa-commenting {
  display: none;
}

.chat-btn i {
  font-size: 22px;
  color: #fff !important;
}

#check:checked ~ .chat-wrapper {
  opacity: 1;
}

.header {
  padding: 13px;
  background-color: blue;
  border-radius: 5px 5px 0px 0px;
  margin-bottom: 10px;
  color: #fff;
}

.chat-form {
  padding: 15px;
}

.chat-form input,
textarea,
button {
  margin-bottom: 10px;
}

.chat-form textarea {
  resize: none;
}

.form-control:focus,
.btn:focus {
  box-shadow: none;
}

.btn,
.btn:focus,
.btn:hover {
  background-color: blue;
  border: blue;
}

#check {
  display: none !important;
}
</style>