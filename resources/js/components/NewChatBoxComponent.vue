<template>
  <div>
    <div class="chat-window col-xs-5 col-md-3 p-0">
      <div class="col-xs-12 col-md-12 p-0">
        <div class="panel panel-default">
          <div class="panel-heading top-bar">
            <div class="col-md-8 col-xs-8">
              <h3 class="panel-title">
                <span class="glyphicon glyphicon-comment"></span>Chat with
                {{ userName }}
              </h3>
            </div>
            <div class="col-md-4 col-xs-4">
              <a href="#"
                ><span
                  id="minim_chat_window"
                  class="fas fa-minus icon_minim"
                  @click="minimize(panelFooter)"
                ></span
              ></a>
              <a href="#"
                ><span
                  class="fas fa-times icon_close"
                  @click="removeDiv(`componentContainer${toUserID}`, toUserID)"
                  data-id="chat_window_1"
                ></span
              ></a>
            </div>
          </div>
          <ul
            class="panel-body msg_container_base"
            :id="panelFooter"
            v-chat-scroll
          >
            <li v-for="(message, index) in messages" :key="index">
              <div v-if="message.from == toUserID">
                <div class="row msg_container base_sent">
                  <div class="col-md-10 col-xs-10">
                    <div
                      class="messages msg_sent"
                      style="
                        background-color: #0078ff;
                        color: white;
                        border-radius: 20px;
                      "
                    >
                      <p style="overflow-wrap: break-word">
                        {{ message.message }}
                      </p>
                      <!-- <time>{{message.created_at | moment("from", "now")}}</time> -->
                      <time style="color: white">{{ message.created_at }}</time>
                      <img
                        style="height: 150px; width: 150px"
                        v-if="message.type == 'image'"
                        :src="'/chat/' + message.file"
                        alt=""
                      />
                      <a
                        :href="'chat/' + message.file"
                        download
                        style="color: black"
                        v-if="message.type == 'file'"
                        ><i class="fas fa-paperclip"></i
                      ></a>
                    </div>
                  </div>
                  <!-- <div class="col-md-2 col-xs-2 avatar">
                                        <img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg"
                                            class=" img-responsive ">
                                    </div> -->
                </div>
              </div>
              <div v-else>
                <div class="row msg_container base_receive">
                  <div class="col-md-10 col-xs-10">
                    <div
                      class="messages msg_receive"
                      style="
                        background-color: #B8B8B8
                        color: black;
                        border-radius: 20px;
                      "
                    >
                      <p style="overflow-wrap: break-word; color: black">
                        {{ message.message }}
                      </p>
                      <time style="color: black">{{
                        message.time ? message.time : message.created_at
                      }}</time>

                      <img
                        style="height: 150px; width: 150px"
                        v-if="message.type == 'image'"
                        :src="'/chat/' + message.file"
                        alt=""
                      />

                      <a
                        :href="'chat/' + message.file"
                        style="color: black"
                        download
                        v-if="message.type == 'file'"
                        ><i class="fas fa-paperclip"></i
                      ></a>

                      <!-- <time>{{
                        message.created_at | moment("from", "now")
                      }}</time> -->
                    </div>
                  </div>
                  <!-- <div class="col-md-2 col-xs-2 avatar">
                                        <img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg"
                                            class=" img-responsive ">
                                    </div> -->
                </div>
              </div>
            </li>

            <!-- <ul style="height:252px;overflow-y:scroll" class="list-unstyled" v-chat-scroll>
                    <li v-for="(message, index) in messages" :key="index" style="margin-bottom:10px">
                    <strong>{{ message.user.name }}</strong>
                        {{message.message}}
                    </li>
                </ul> -->
          </ul>
          <div class="panel-footer" :class="panelFooter">
            <div class="input-group">
              <input
                id="btn-input"
                type="text"
                class="form-control input-sm chat_input"
                placeholder="Write your message here..."
                v-model="newMessage"
                @keyup.enter="sendMessage"
              />
              <span class="input-group-btn">
                <button class="btn btn-primary btn-sm btn-chat" id="btn-chat">
                  Send
                </button>
              </span>
            </div>
            <div style="display: flex; height: 30px">
              <input
                accept="image/*"
                type="file"
                style="
                  position: absolute;
                  width: 26px;
                  font-size: 26px;
                  cursor: pointer;
                "
                class="far fa-image"
                @change="sendImage"
              />
              <input
                accept="application/*"
                type="file"
                style="
                  position: absolute;
                  width: 23px;
                  font-size: 26px;
                  margin-left: 50px;
                  cursor: pointer;
                "
                class="fas fa-paperclip"
                @change="sendFile"
              />

              <!-- <span class="input-group-btn">
                <button class="btn btn-primary btn-sm btn-chat" id="btn-chat">
                  Send
                </button>
              </span> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script>
import $ from "jquery";
window.Vue = require("vue");

export default {
  props: [
    "chatBoxStyle",
    "userName",
    "panelFooter",
    "fromUserID",
    "toUserID",
    "usersOpenedChatArray",
  ],
  mounted() {},
  data() {
    return {
      messages: [],
      newMessage: "",
      users: [],
      opacityClass: false,
      time: new Date(),
      image: null,
    };
  },
  created() {
    //when the component is created run the fetchMessages function, and join the unique private channel,
    //and listen to the message sent event, and if a message was sent push it in the messages array.
    this.fetchMessages(this.fromUserID, this.toUserID);
    window.Echo.private(
      `messages.${this.toUserID}.${this.fromUserID}`
    ).listen("MessageSentEvent", (event) => this.messages.push(event.message));
  },

  methods: {
    //this method is resonsable for toggling the chatBox opacity.
    toggleOpacity(event) {
      event.target
        .closest("div")
        .querySelector(
          ".chat-wrapper"
        ).style.marginRight += `${this.componentContainersWidth}px`;
      this.opacityClass = !this.opacityClass;
    },

    fetchMessages(from, to) {
      //get all messages between these 2 users and pass the returned messages to the messages array
      axios
        .get(`/messages?from=${from}&to=${to}`)
        .then((response) => (this.messages = response.data));
    },

    sendMessage(event) {
      //send a new message to the backend and push this message to the messages array,
      //so that it appears in the chat box to the user that sent it.
      if (this.newMessage === "") return false;
      axios
        .post("/chat", {
          message: this.newMessage,
          fromUser: this.fromUserID,
          toUser: this.toUserID,
        })
        .then((resonse) => console.log(resonse.data.message));
      const time = new Date();
      this.messages.push({
        message: this.newMessage,
        user: this.user,
        time: time.toLocaleString("en-US", {
          hour: "numeric",
          minute: "numeric",
          hour12: true,
        }),
      });
      this.newMessage = "";
    },

    async sendFile(event) {
      //this method is responsible of sending attachements files to the backend.
      let data = new FormData();
      this.image = event.target.files[0];
      data.append("image", event.target.files[0]);
      data.append("toUser", this.toUserID);
      data.append("fromUser", this.fromUserID);
      const response = await axios.post("/chat", data);
      const time = new Date();

      this.messages.push({
        message: this.newMessage,
        user: this.user,
        file: response.data.message.file,
        type: "file",
        time: time.toLocaleString("en-US", {
          hour: "numeric",
          minute: "numeric",
          hour12: true,
        }),
      });
    },

    async sendImage(event) {
      //this method is responsible of sending images to the backend.
      let data = new FormData();
      this.image = event.target.files[0];
      if (this.image.size > 20048) {
        alert("Max image size is 2MB");
        return false;
      }
      data.append("image", event.target.files[0]);
      data.append("toUser", this.toUserID);
      data.append("fromUser", this.fromUserID);
      const response = await axios.post("/chat", data);
      const time = new Date();

      this.messages.push({
        message: this.newMessage,
        user: this.user,
        file: response.data.message.file,
        type: "image",
        time: time.toLocaleString("en-US", {
          hour: "numeric",
          minute: "numeric",
          hour12: true,
        }),
      });
    },

    minimize(value) {
      //this message is responsible for minimizing the chat box.
      const expandSpan = event.target;
      if (!expandSpan.classList.contains("panel-collapsed")) {
        $(`#${value}`).slideUp();
        expandSpan.classList.add("panel-collapsed");
        document
          .querySelector(".panel-footer")
          .classList.add("panel-collapsed");
        document.querySelector(`.${value}`).style.display = "none";
        expandSpan.classList.remove("fa-minus");
        expandSpan.classList.add("fa-plus");
      } else {
        $(`#${value}`).slideDown();
        expandSpan.classList.remove("panel-collapsed");
        document.querySelector(`.${value}`).style.display = "block";
        expandSpan.classList.remove("fa-plus");
        expandSpan.classList.add("fa-minus");
      }
    },
    removeDiv(divClass, userID) {
      //this method is responsible for closing the chat box and updating the array in the session storage
      let previouslyOpenedUsersChats = JSON.parse(
        sessionStorage.getItem("usersOpenedChat")
      );

      previouslyOpenedUsersChats = previouslyOpenedUsersChats.filter((el) => {
        return el.id != userID;
      });

      sessionStorage.setItem(
        "usersOpenedChat",
        JSON.stringify(previouslyOpenedUsersChats)
      );

      document.getElementById(`${divClass}`).remove();
    },
  },
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

.panel-footer {
  padding: 10px 15px;
  background-color: #f5f5f5;
  border-top: 1px solid #ddd;
  border-bottom-right-radius: 3px;
  border-bottom-left-radius: 3px;
}
.panel-title {
  font-size: 16px;
  margin-bottom: 0;
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
  width: 360px;
}
.chat-window > div > .panel {
  border-radius: 5px 5px 0 0;
}
.icon_minim {
  padding: 2px 10px;
}
.msg_container_base {
  background: rgb(243, 241, 241);
  margin: 0;
  padding: 0 10px 10px;
  height: 300px;
  overflow-x: hidden;
}
.top-bar {
  background: #666;
  color: white;
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

@media only screen and (max-width: 1000px) {
  .col-xs-4 {
    display: contents;
  }
}
</style>