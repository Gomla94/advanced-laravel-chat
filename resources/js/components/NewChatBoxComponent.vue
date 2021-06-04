<template>
  <div>
    <div class="chat-window col-xs-5 col-md-3 p-0">
      <div class="col-xs-12 col-md-12 p-0">
        <div class="panel panel-default">
          <div class="panel-heading top-bar">
            <img
              v-if="userimage"
              :src="'/images/' + userimage"
              width="25px"
              height="25px"
              alt=""
            />
            <div class="col-md-8 col-xs-8">
              <h3 class="panel-title">
                <span class="glyphicon glyphicon-comment"></span>

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
                  @click="removeDiv(`${componentID}`, toUserID, groupID)"
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
              <div v-if="message.from === fromUserID">
                <div class="row msg_container base_receive">
                  <div class="col-md-10 col-xs-10">
                    <div
                      class="messages msg_receive"
                      style="
                        background-color: #b8b8b8;
                        color: black;
                        border-radius: 20px;
                      "
                    >
                      <!-- <div
                        class="fas fa-reply replyBtn"
                        style="float: right; cursor: pointer"
                        @click="messageReply($event, message.id)"
                      ></div> -->

                      <div
                        v-if="
                          message.replyingToMessage &&
                          message.replyingToMessage.type == null
                        "
                        class="messageReply"
                        style="
                          height: auto;
                          width: auto;
                          background-color: grey;
                          opacity: 30%;
                          padding: 10px;
                          color: black;
                          margin-top: 10px;
                          border-radius: 5px;
                          border-left: 3px solid red;
                        "
                      >
                        <p class="messageReplyText">
                          {{ message.replyingToMessage.user.name }}:
                          {{ message.replyingToMessage.message }}
                        </p>
                      </div>

                      <div
                        v-if="
                          message.replyingToMessage &&
                          message.replyingToMessage.type
                        "
                        class="messageReply"
                        style="
                          height: auto;
                          width: auto;
                          background-color: grey;
                          opacity: 30%;
                          padding: 10px;
                          color: black;
                          margin-top: 10px;
                          border-radius: 5px;
                          border-left: 3px solid red;
                        "
                      >
                        {{ message.replyingToMessage.user.name }}:
                        <img
                          class="messageReplyImage"
                          style="width: 40px"
                          :src="'/chat/' + message.replyingToMessage.file"
                          v-if="message.replyingToMessage.type == 'image'"
                          height="50px"
                          width="50px"
                          alt=""
                        />
                        <a
                          class="messageReplyAttachment"
                          style="color: black"
                          v-if="message.replyingToMessage.type == 'file'"
                          ><i class="fas fa-paperclip"></i
                        ></a>
                        <i
                          class="fas fa-microphone messageReplyAudio"
                          v-if="message.replyingToMessage.type == 'audio'"
                          style="width: -webkit-fill-available"
                        ></i>
                      </div>

                      <div
                        class="forwarded"
                        v-if="message.forwarded === 1"
                        style="
                          background-color: grey;
                          opacity: 0.3;
                          height: 30px;
                          width: 100%;
                          font-weight: lighter;
                        "
                      >
                        <i class="fas fa-arrow-right"></i>
                        forwarded
                      </div>
                      <p class="message" style="overflow-wrap: break-word">
                        <strong>{{ message.user.name }}: </strong
                        >{{ message.message }}
                      </p>
                      <!-- <time>{{message.created_at | moment("from", "now")}}</time> -->
                      <time style="color: black">{{
                        message.time ? message.time : message.created_at
                      }}</time>
                      <img
                        class="messageImage"
                        v-if="message.type == 'image'"
                        :src="'/chat/' + message.file"
                        alt=""
                      />
                      <a
                        class="messageAttachment"
                        :href="'chat/' + message.file"
                        download
                        style="color: black"
                        v-if="message.type == 'file'"
                        ><i class="fas fa-paperclip"></i
                      ></a>

                      <audio
                        class="messageAudio"
                        controls="true"
                        :src="'/chat/' + message.file"
                        v-if="message.type == 'audio'"
                        style="width: -webkit-fill-available"
                      ></audio>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <div class="row msg_container base_sent">
                  <div class="col-md-10 col-xs-10">
                    <div
                      class="messages msg_sent"
                      style="
                        background-color: #0078ff;
                        color: black;
                        border-radius: 20px;
                      "
                    >
                      <div
                        class="messageBtns"
                        style="display: flex; justify-content: flex-end"
                      >
                        <div
                          class="fas fa-arrow-right forwardBtn"
                          style="cursor: pointer; margin-right: 20px"
                          @click="getUserRelatedUsersAnGroups($event, message)"
                        ></div>
                        <div
                          class="fas fa-reply replyBtn"
                          style="float: right; cursor: pointer"
                          @click="messageReply($event, message.id)"
                        ></div>
                      </div>

                      <div
                        v-if="
                          message.replyingToMessage &&
                          message.replyingToMessage.message
                        "
                        class="messageReply"
                        style="
                          height: auto;
                          width: auto;
                          background-color: grey;
                          opacity: 30%;
                          padding: 10px;
                          color: black;
                          margin-top: 10px;
                          border-radius: 5px;
                          border-left: 3px solid red;
                          margin-bottom: 5px;
                        "
                      >
                        {{ message.replyingToMessage.user.name }}:
                        {{ message.replyingToMessage.message }}
                      </div>

                      <div
                        v-if="
                          message.replyingToMessage &&
                          message.replyingToMessage.type
                        "
                        class="messageReply"
                        style="
                          height: auto;
                          width: auto;
                          background-color: grey;
                          opacity: 30%;
                          padding: 10px;
                          color: black;
                          margin-top: 10px;
                          border-radius: 5px;
                          border-left: 3px solid red;
                          margin-bottom: 5px;
                        "
                      >
                        {{ message.replyingToMessage.user.name }}:
                        <img
                          class="messageReplyImage"
                          style="width: 40px"
                          :src="'/chat/' + message.replyingToMessage.file"
                          v-if="message.replyingToMessage.type == 'image'"
                          height="50px"
                          width="50px"
                          alt=""
                        />
                        <a
                          class="messageReplyAttachment"
                          style="color: black"
                          v-if="message.replyingToMessage.type == 'file'"
                          ><i class="fas fa-paperclip"></i
                        ></a>
                        <i
                          class="fas fa-microphone messageReplyAudio"
                          v-if="message.replyingToMessage.type == 'audio'"
                          style="width: -webkit-fill-available"
                        ></i>
                      </div>

                      <div
                        class="forwarded"
                        v-if="message.forwarded == 1"
                        style="
                          background-color: grey;
                          opacity: 0.3;
                          height: 30px;
                          width: 100%;
                        "
                      >
                        <i class="fas fa-arrow-right"></i>
                        forwarded
                      </div>
                      <p
                        class="messageText"
                        style="overflow-wrap: break-word; color: white"
                      >
                        <strong>{{ message.user.name }}: </strong
                        >{{ message.message }}
                      </p>
                      <time style="color: white">{{ message.created_at }}</time>

                      <img
                        class="messageImage"
                        v-if="message.type == 'image'"
                        :src="'/chat/' + message.file"
                        alt=""
                      />

                      <a
                        class="messageAttachment"
                        :href="'chat/' + message.file"
                        style="color: black"
                        download
                        v-if="message.type == 'file'"
                        ><i class="fas fa-paperclip"></i
                      ></a>

                      <audio
                        class="messageAudio"
                        controls="true"
                        :src="'/chat/' + message.file"
                        v-if="message.type == 'audio'"
                        style="width: -webkit-fill-available"
                      ></audio>
                      <!-- <time>{{
                        message.created_at | moment("from", "now")
                      }}</time> -->
                    </div>
                  </div>
                </div>
              </div>
            </li>
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
              <i
                class="fa fa-microphone"
                style="
                  cursor: pointer;
                  font-size: 25px;
                  margin-left: 100px;
                  margin-top: 3px;
                "
                @click="startRecording"
              ></i>
              <i
                class="fas fa-stop-circle hideBtn"
                style="cursor: pointer; font-size: 25px; margin-top: 3px"
                @click="stopRecording"
              ></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


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
    "componentID",
    "groupID",
    "authuser",
    "userimage",
    "replyMessageID",
    "replyingToMessage",
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
      recorder: null,
      input: null,
    };
  },
  created() {
    //when the component is created run the fetchMessages function, and join the unique private channel,
    //and listen to the message sent event, and if a message was sent push it in the messages array.
    this.fetchMessages(this.fromUserID, this.toUserID, this.groupID);
    window.Echo.private(
      `messages.${this.toUserID}.${this.fromUserID}`
    ).listen("MessageSentEvent", (event) => this.messages.push(event.message));

    if (this.groupID) {
      window.Echo.private(`group.${this.groupID}`).listen(
        "MessageSentEvent",
        (event) => this.messages.push(event.message)
      );
    }
    console.log(this.userName);
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

    fetchMessages(from, to, groupid) {
      //get all messages between these 2 users and pass the returned messages to the messages array
      axios
        .get(`/messages?from=${from}&to=${to}&groupid=${groupid}`)
        .then((response) => {
          this.messages = response.data;
          // console.log(response.data);
        });
    },

    async sendMessage(
      event,
      passedMessage,
      passedToUser,
      passedGroupID,
      forwarded
    ) {
      const replyDiv = event
        ? event.target.closest(".panel-footer").querySelector(".replyDiv")
        : null;
      if (replyDiv) replyDiv.remove();

      // console.log(passedGroupID);
      // return false;

      //send a new message to the backend and push this message to the messages array,
      //so that it appears in the chat box to the user that sent it.
      // this.newMessage = passedMessage ? passedMessage : "";
      if (passedMessage !== undefined) {
        console.log("passedMessage");
        this.newMessage = passedMessage;
      }
      if (this.newMessage === "") return false;

      const time = new Date();
      const currentTime = time.toLocaleString("en-US", {
        hour: "numeric",
        minute: "numeric",
        hour12: true,
      });

      axios
        .post("/chat", {
          message: passedMessage ? passedMessage : this.newMessage,
          fromUser: this.fromUserID,
          toUser: passedGroupID
            ? null
            : passedToUser
            ? passedToUser
            : this.toUserID,
          groupID: passedToUser
            ? null
            : passedGroupID
            ? passedGroupID
            : this.groupID,
          created_at: currentTime,
          replyToMessageID: this.replyMessageID,
          forwarded: forwarded !== undefined ? 1 : 0,
        })
        .then((response) => console.log("response"));
      if (!passedMessage) {
        this.messages.push({
          message: this.newMessage,
          from: this.fromUserID,
          user: { name: this.authuser.name },
          created_at: currentTime,
          replyingToMessage: this.replyingToMessage,
        });
      }

      let messagesList;
      if (passedToUser || passedGroupID) {
        const forwardToExistedUserChatWindow = document.querySelector(
          `#componentContainer${passedToUser}`
        );

        const forwardToExistedGroupChatWindow = document.querySelector(
          `#componentContainerGroup${passedGroupID}`
        );

        console.log(
          forwardToExistedUserChatWindow,
          forwardToExistedGroupChatWindow
        );

        messagesList = forwardToExistedUserChatWindow
          ? forwardToExistedChatWindow.querySelector(
              `#panel-footer-${passedToUser}`
            )
          : forwardToExistedGroupChatWindow.querySelector(
              `#panel-footer-group-${passedGroupID}`
            );
      }

      if (messagesList) {
        console.log("found");
        // const messagesList = forwardToExistedChatWindow.querySelector(
        //   `#panel-footer-${passedToUser}`
        // );
        const forwardedMessage = {
          message: this.newMessage,
          from: this.fromUserID,
          user: { name: this.authuser.name },
          created_at: currentTime,
          replyingToMessage: this.replyingToMessage,
        };
        const myForwardedMessage = document.createElement("li");
        myForwardedMessage.style.marginLeft = "-20px";
        // myForwardedMessage.style.width = "380px";
        myForwardedMessage.style.marginTop = "10px";
        myForwardedMessage.innerHTML = `
            <div class="col-md-10 col-xs-10">
              <div class="messages msg_receive appendForwardedMessage">
                <div class="messages msg_receive" style="background-color: grey; opacity: 0.3; height: 30px; width: 100%;">
                  <i class="fas fa-arrow-right"><span style="font-family:Nunito"></span></i>
                  forwarded
                </div>
                <p class="message" style="overflow-wrap: break-word;margin-bottom:4px">
                    <strong>${forwardedMessage.user.name}: </strong>${forwardedMessage.message}
                </p>
                <time style="color:black;font-size:11px">${forwardedMessage.created_at}</time>
              </div>
            </div>
        `;

        messagesList.append(myForwardedMessage);
      }
      // console.log(this.messages);
      // console.log(this.newMessage);

      this.newMessage = "";
      this.replyingToMessage = null;
      this.replyMessageID = null;
      this.groupID = null;
    },

    async sendFile(
      event,
      passedImage,
      passedUser,
      passedType,
      passedGroup,
      forwarded
    ) {
      // console.log(forwarded);
      // return false;
      //this method is responsible of sending attachements files to the backend.

      const replyDiv = event
        ? event.target.closest(".panel-footer").querySelector(".replyDiv")
        : null;

      console.log(passedGroup, passedUser, this.toUserID);
      // return false;
      if (replyDiv) replyDiv.remove();

      const time = new Date();
      const currentTime = time.toLocaleString("en-US", {
        hour: "numeric",
        minute: "numeric",
        hour12: true,
      });

      let data = new FormData();
      this.image = event ? event.target.files[0] : null;
      data.append("image", event ? event.target.files[0] : null);
      data.append(
        "toUser",
        passedGroup !== undefined
          ? null
          : passedUser !== undefined
          ? passedUser
          : this.toUserID
      );
      data.append("fromUser", this.fromUserID);
      data.append("type", passedType);
      data.append("file", passedImage);
      data.append("forwarded", forwarded !== undefined ? 1 : 0);
      data.append("created_at", currentTime);
      if (this.replyMessageID) {
        data.append("replyToMessageID", this.replyMessageID);
      }
      data.append(
        "groupID",
        passedUser ? null : passedGroup ? passedGroup : this.groupID
      ); // return false;

      console.log(data);
      // return false;
      const response = await axios.post("/chat", data);
      console.log(response.data.message);
      // return false;
      if (!passedImage) {
        this.messages.push({
          id: response.data.message.id,
          message: this.newMessage,
          user: this.user,
          file: response.data.message.file,
          type: "file",
          from: this.fromUserID,
          user: { name: this.authuser.name },
          time: currentTime,
          replyingMessageID: this.replyMessageID,
          replyingToMessage: this.replyingToMessage,
        });
      }

      let messagesList;
      if (passedUser || passedGroup) {
        const forwardToExistedUserChatWindow = document.querySelector(
          `#componentContainer${passedUser}`
        );

        const forwardToExistedGroupChatWindow = document.querySelector(
          `#componentContainerGroup${passedGroup}`
        );

        console.log(
          forwardToExistedUserChatWindow,
          forwardToExistedGroupChatWindow
        );

        messagesList = forwardToExistedUserChatWindow
          ? forwardToExistedChatWindow.querySelector(
              `#panel-footer-${passedUser}`
            )
          : forwardToExistedGroupChatWindow.querySelector(
              `#panel-footer-group-${passedGroup}`
            );
      }

      if (messagesList) {
        const forwardedMessage = {
          id: response.data.message.id,
          message: this.newMessage,
          user: this.user,
          file: response.data.message.file,
          type: "image",
          from: this.fromUserID,
          user: { name: this.authuser.name },
          created_at: currentTime,
          replyingMessageID: this.replyMessageID,
          replyingToMessage: this.replyingToMessage,
        };
        const myForwardedMessage = document.createElement("li");
        myForwardedMessage.style.marginLeft = "-20px";
        // myForwardedMessage.style.width = "380px";
        myForwardedMessage.style.marginTop = "10px";
        myForwardedMessage.innerHTML = `
            <div class="col-md-10 col-xs-10">
              <div class="messages msg_receive appendForwardedMessage">
                <div class="messages msg_receive" style="background-color: grey; opacity: 0.3; height: 30px; width: 100%;">
                  <i class="fas fa-arrow-right"><span style="margin-left:4px;font-family:Nunito">forwarded</span></i>
                </div>
                <p class="message" style="overflow-wrap: break-word;margin-bottom:4px">
                    <strong>${forwardedMessage.user.name}: </strong>
                </p>
                <time style="color:black;font-size:11px">${forwardedMessage.created_at}</time>

                <a href="/chat/${forwardedMessage.file}" download style="cursor:pointer;color:black">
                <i class="fas fa-paperclip""></i>
                </a>
              </div>
            </div>
        `;

        messagesList.append(myForwardedMessage);
      }

      this.newMessage = null;
      this.replyingToMessage = null;
      this.replyMessageID = null;
      this.groupID = null;
      if (event) event.target.value = "";
    },

    async sendImage(
      event,
      passedImage,
      passedUser,
      passedType,
      passedGroup,
      forwarded
    ) {
      //this method is responsible of sending images to the backend.

      console.log("forward image");
      console.log(passedGroup);
      // return false;
      const replyDiv = event
        ? event.target.closest(".panel-footer").querySelector(".replyDiv")
        : null;
      if (replyDiv) replyDiv.remove();

      const time = new Date();
      const currentTime = time.toLocaleString("en-US", {
        hour: "numeric",
        minute: "numeric",
        hour12: true,
      });

      let data = new FormData();
      if (event !== null) {
        this.image = event.target.files[0];
        if (this.image.size > 20048) {
          alert("Max image size is 2MB");
          return false;
        }
      }

      console.log(passedUser, passedGroup, this.toUserID);
      // return false;
      data.append("image", event !== null ? event.target.files[0] : null);
      data.append("file", passedImage);
      data.append(
        "toUser",
        passedGroup ? null : passedUser ? passedUser : this.toUserID
      );
      data.append("type", passedType ? passedType : null);
      data.append("fromUser", this.fromUserID);
      data.append("created_at", currentTime);
      data.append("forwarded", forwarded !== undefined ? 1 : 0);
      if (this.replyMessageID) {
        data.append("replyToMessageID", this.replyMessageID);
      }

      data.append(
        "groupID",
        passedUser ? null : passedGroup ? passedGroup : this.groupID
      );

      const response = await axios.post("/chat", data);

      // return false;
      if (!passedImage) {
        this.messages.push({
          id: response.data.message.id,
          message: this.newMessage,
          user: this.user,
          file: response.data.message.file,
          type: "image",
          from: this.fromUserID,
          user: { name: this.authuser.name },
          time: currentTime,
          replyingMessageID: this.replyMessageID,
          replyingToMessage: this.replyingToMessage,
        });
      }

      const forwardToExistedUserChatWindow = document.querySelector(
        `#componentContainer${passedUser}`
      );

      const forwardToExistedGroupChatWindow = document.querySelector(
        `#componentContainerGroup${passedGroup}`
      );

      console.log(
        forwardToExistedUserChatWindow,
        forwardToExistedGroupChatWindow
      );

      let messagesList;
      if (passedUser || passedGroup) {
        console.log("passedMessage");
        const forwardToExistedUserChatWindow = document.querySelector(
          `#componentContainer${passedUser}`
        );

        const forwardToExistedGroupChatWindow = document.querySelector(
          `#componentContainerGroup${passedGroup}`
        );

        console.log(
          forwardToExistedUserChatWindow,
          forwardToExistedGroupChatWindow
        );

        messagesList = forwardToExistedUserChatWindow
          ? forwardToExistedChatWindow.querySelector(
              `#panel-footer-${passedUser}`
            )
          : forwardToExistedGroupChatWindow.querySelector(
              `#panel-footer-group-${passedGroup}`
            );
      }

      if (messagesList) {
        console.log("messageList");
        // const messagesList = forwardToExistedChatWindow.querySelector(
        //   `#panel-footer-${passedUser}`
        // );
        const forwardedMessage = {
          message: this.newMessage,
          from: this.fromUserID,
          file: response.data.message.file,
          type: "image",
          user: { name: this.authuser.name },
          created_at: currentTime,
          replyingToMessage: this.replyingToMessage,
        };
        const myForwardedMessage = document.createElement("li");
        myForwardedMessage.style.marginLeft = "-20px";
        // myForwardedMessage.style.width = "380px";
        myForwardedMessage.style.marginTop = "10px";
        myForwardedMessage.innerHTML = `
            <div class="col-md-10 col-xs-10">
              <div class="messages msg_receive appendForwardedMessage">
                <div class="messages msg_receive" style="background-color: grey; opacity: 0.3; height: 30px; width: 100%;">
                  <i class="fas fa-arrow-right"><span style="margin-left:4px;font-family:Nunito">forwarded</span></i>
                </div>
                <p class="message" style="overflow-wrap: break-word;margin-bottom:4px">
                    <strong>${forwardedMessage.user.name}: </strong>
                </p>
                <time style="color:black;font-size:11px;position:absolute">${forwardedMessage.created_at}</time>
                <img src="/chat/${forwardedMessage.file}" class="appendForwardedImage">
              </div>
            </div>
        `;

        messagesList.append(myForwardedMessage);
      }

      this.newMessage = null;
      this.replyingToMessage = null;
      this.replyMessageID = null;
      this.groupID = null;
      if (event) event.target.value = "";
    },

    startRecording(event) {
      const audioContext = new AudioContext();
      const recordingBtn = event.target;
      const stopRecordingBtn = recordingBtn.nextElementSibling;

      navigator.mediaDevices
        .getUserMedia({ audio: true })
        .then((stream) => {
          console.log(
            "getUserMedia() success, stream created, initializing Recorder.js ..."
          );

          stopRecordingBtn.style.marginLeft = "100px";
          recordingBtn.classList.toggle("hideBtn");
          stopRecordingBtn.classList.toggle("hideBtn");
          /* assign to gumStream for later use */
          // audioStream = stream;
          /* use the stream */
          this.input = audioContext.createMediaStreamSource(stream);
          /* Create the Recorder object and configure to record mono sound (1 channel) Recording 2 channels will double the file size */
          this.recorder = new Recorder(this.input);
          // //start the recording process
          this.recorder.record();
          console.log("Recording started");
        })
        .catch(function (err) {
          //enable the record button if getUserMedia() fails
          console.log(err);
        });
    },

    stopRecording(event) {
      const replyDiv = event.target
        .closest(".panel-footer")
        .querySelector(".replyDiv");

      if (replyDiv) replyDiv.remove();
      this.recorder.stop();
      const stopRecordingBtn = event.target;
      const startRecordingBtn = stopRecordingBtn.previousElementSibling;
      console.log(startRecordingBtn);
      startRecordingBtn.style.marginLeft = "100px";
      startRecordingBtn.classList.toggle("hideBtn");
      stopRecordingBtn.classList.toggle("hideBtn");
      this.recorder.exportWAV(this.createDownloadLink);
    },

    async createDownloadLink(blob) {
      this.sendVoice(blob);
    },

    async sendVoice(
      blob,
      passedVoice,
      passedUser,
      passedType,
      passedGroup,
      forwarded
    ) {
      console.log("forward voice");

      console.log(passedUser, passedGroup);
      // return false;
      const time = new Date();
      const currentTime = time.toLocaleString("en-US", {
        hour: "numeric",
        minute: "numeric",
        hour12: true,
      });

      const formdata = new FormData();
      formdata.append("image", blob !== null ? blob : null);
      formdata.append("file", passedVoice);
      formdata.append("toUser", passedUser ? passedUser : this.toUserID);
      formdata.append("fromUser", this.fromUserID);
      formdata.append("type", passedType ? passedType : null);
      formdata.append("created_at", currentTime);
      formdata.append("forwarded", forwarded !== undefined ? 1 : 0);
      if (this.replyMessageID) {
        formdata.append("replyToMessageID", this.replyMessageID);
      }
      formdata.append(
        "groupID",
        passedUser ? null : passedGroup ? passedGroup : this.groupID
      );
      const response = await axios.post("/chat", formdata);

      console.log(response);

      if (!passedVoice) {
        this.messages.push({
          id: response.data.message.id,
          message: this.newMessage,
          user: this.user,
          file: response.data.message.file,
          type: "audio",
          from: this.fromUserID,
          user: { name: this.authuser.name },
          time: currentTime,
          replyingMessageID: this.replyMessageID,
          replyingToMessage: this.replyingToMessage,
        });
      }

      let messagesList;
      if (passedUser || passedGroup) {
        const forwardToExistedUserChatWindow = document.querySelector(
          `#componentContainer${passedUser}`
        );

        const forwardToExistedGroupChatWindow = document.querySelector(
          `#componentContainerGroup${passedGroup}`
        );

        console.log(
          forwardToExistedUserChatWindow,
          forwardToExistedGroupChatWindow
        );

        messagesList = forwardToExistedUserChatWindow
          ? forwardToExistedChatWindow.querySelector(
              `#panel-footer-${passedUser}`
            )
          : forwardToExistedGroupChatWindow.querySelector(
              `#panel-footer-group-${passedGroup}`
            );
      }

      if (messagesList) {
        console.log("audio message list");
        const forwardedMessage = {
          id: response.data.message.id,
          message: this.newMessage,
          user: this.user,
          file: response.data.message.file,
          type: "image",
          from: this.fromUserID,
          user: { name: this.authuser.name },
          created_at: currentTime,
          replyingMessageID: this.replyMessageID,
          replyingToMessage: this.replyingToMessage,
        };
        const myForwardedMessage = document.createElement("li");
        myForwardedMessage.style.marginLeft = "-20px";
        // myForwardedMessage.style.width = "380px";
        myForwardedMessage.style.marginTop = "10px";
        myForwardedMessage.innerHTML = `
            <div class="col-md-10 col-xs-10">
              <div class="messages msg_receive appendForwardedMessage">
                <div class="messages msg_receive" style="background-color: grey; opacity: 0.3; height: 30px; width: 100%;">
                  <i class="fas fa-arrow-right"><span style="margin-left:4px;font-family:Nunito">forwarded</span></i>
                </div>
                <p class="message" style="overflow-wrap: break-word;margin-bottom:4px">
                    <strong>${forwardedMessage.user.name}: </strong>
                </p>
                <time style="color:black;font-size:11px">${forwardedMessage.created_at}</time>

                <audio controls style="width:-webkit-fill-available" src="/chat/${forwardedMessage.file}">
              </div>
            </div>
        `;

        messagesList.append(myForwardedMessage);
      }

      this.replyingToMessage = null;
      this.replyMessageID = null;
      this.groupID = null;
    },

    minimize(value) {
      console.log(value);
      //this message is responsible for minimizing the chat box.
      const expandSpan = event.target;
      if (!expandSpan.classList.contains("panel-collapsed")) {
        console.log("if", value);
        $(`#${value}`).slideUp();
        expandSpan.classList.add("panel-collapsed");
        document
          .querySelector(".panel-footer")
          .classList.add("panel-collapsed");
        document.querySelector(`.${value}`).style.display = "none";
        expandSpan.classList.remove("fa-minus");
        expandSpan.classList.add("fa-plus");
      } else {
        console.log("else", value);

        $(`#${value}`).slideDown();
        expandSpan.classList.remove("panel-collapsed");
        document.querySelector(`.${value}`).style.display = "block";
        expandSpan.classList.remove("fa-plus");
        expandSpan.classList.add("fa-minus");
      }
    },

    removeDiv(divClass, userID, groupID) {
      //this method is responsible for closing the chat box and updating the array in the session storage
      let previouslyOpenedUsersChats = JSON.parse(
        sessionStorage.getItem("usersOpenedChat")
      );

      if (groupID !== undefined) {
        previouslyOpenedUsersChats = previouslyOpenedUsersChats.filter((el) => {
          return el.groupid !== groupID;
        });
      } else {
        previouslyOpenedUsersChats = previouslyOpenedUsersChats.filter((el) => {
          return el.id !== userID;
        });
      }

      sessionStorage.setItem(
        "usersOpenedChat",
        JSON.stringify(previouslyOpenedUsersChats)
      );

      document.getElementById(`${divClass}`).remove();
    },

    messageReply(event, messageid) {
      // console.log(messageid);
      const replyBtn = event.target;
      const chatBoxFooter = replyBtn
        .closest(".panel")
        .querySelector(".panel-footer");

      const messageDiv = replyBtn.closest(".msg_sent");
      const existedReplyDiv = messageDiv.querySelector(".messageReply");
      const existedForwardDiv = messageDiv.querySelector(".forwarded");
      console.log(messageDiv, existedReplyDiv, existedForwardDiv);
      // return false;
      let appendreplyingDiv;
      let existedMessageImage = replyBtn
        .closest(".col-md-10")
        .querySelector(".messageImage");

      let existedMessageAudio = replyBtn
        .closest(".col-md-10")
        .querySelector(".messageAudio");

      let existedMessageAttachment = replyBtn
        .closest(".col-md-10")
        .querySelector(".messageAttachment");

      // console.log(existedMessageAudio);

      if (existedReplyDiv && existedForwardDiv) {
        console.log("contains reply and forwarded");
        this.replyingToMessage = {
          message: existedForwardDiv.nextElementSibling.textContent
            .trim()
            .split(" ")
            .splice(1)
            .join(" "),
          user: {
            name: existedForwardDiv.nextElementSibling.textContent
              .trim()
              .split(" ")[0]
              .replace(":", ""),
          },
          file: existedMessageImage
            ? existedMessageImage
                .getAttribute("src")
                .split("")
                .splice(6)
                .join("")
            : null,
          type: existedMessageAudio
            ? "audio"
            : existedMessageImage
            ? "image"
            : existedMessageAttachment
            ? "file"
            : null,
        };
        appendreplyingDiv = messageDiv.querySelector(".messageText")
          .textContent;

        console.log(appendreplyingDiv);
      } else {
        console.log("no reply");
        console.log(messageDiv.querySelector(".messageText"));
        // return false;
        this.replyingToMessage = {
          message: messageDiv
            .querySelector(".messageText")
            .textContent.trim()
            .split(" ")
            .splice(1)
            .join(" "),
          user: {
            name: messageDiv
              .querySelector(".messageText")
              .textContent.split(" ")[0]
              .replace(":", ""),
          },
          file: existedMessageImage
            ? existedMessageImage
                .getAttribute("src")
                .split("")
                .splice(6)
                .join("")
            : null,
          type: existedMessageAudio
            ? "audio"
            : existedMessageImage
            ? "image"
            : existedMessageAttachment
            ? "file"
            : null,
        };

        appendreplyingDiv = messageDiv.querySelector(".messageText")
          .textContent;
      }

      this.replyMessageID = messageid;

      console.log(appendreplyingDiv);
      // return false;

      this.appendReply(
        replyBtn,
        appendreplyingDiv,
        chatBoxFooter,
        existedMessageImage,
        existedMessageAudio,
        existedMessageAttachment
      );

      // return false;

      // console.log(this.replyMessageID);
    },

    appendReply(
      replyBtn,
      appendreplyingDiv,
      chatBoxFooter,
      existedMessageImage = null,
      existedMessageAudio = null,
      existedMessageAttachment = null
    ) {
      const existedReplyDiv = chatBoxFooter.querySelector(".replyDiv");
      if (existedReplyDiv) existedReplyDiv.remove();
      let imageSrc = null;
      if (existedMessageImage) {
        imageSrc = replyBtn
          .closest(".row")
          .querySelector(".messageImage")
          .getAttribute("src");
      }

      // return false;
      const replyingDiv = document.createElement("div");
      replyingDiv.classList.add("replyDiv");
      replyingDiv.style.backgroundColor = "silver";
      replyingDiv.style.borderLeft = "3px solid red";
      if (imageSrc) {
        replyingDiv.innerHTML = `${appendreplyingDiv}<img src="${imageSrc}" class="replyMessageImage" height=50px;width=50px/>`;
      } else if (existedMessageAudio) {
        replyingDiv.innerHTML = `<p style=word-break:break-word>${appendreplyingDiv
          .split(" ")
          .slice(0, 1)
          .join("")} <i class="fas fa-microphone"></i></p>`;
      } else if (existedMessageAttachment) {
        console.log("attachment");
        replyingDiv.innerHTML = `<p style=word-break:break-word>${appendreplyingDiv
          .split(" ")
          .slice(0, 1)
          .join("")} <i class="fas fa-paperclip"></i></p>`;
      } else {
        replyingDiv.innerHTML = `<p style=word-break:break-word>${appendreplyingDiv}</p>`;
      }
      replyingDiv.style.height = "auto";
      replyingDiv.style.width = "auto";
      replyingDiv.style.justifyContent = "start";
      replyingDiv.style.display = "flex";
      replyingDiv.style.borderRadius = "3px";
      replyingDiv.style.padding = "5px";
      const closeReply = document.createElement("i");
      closeReply.classList.add("fas", "fa-times");
      closeReply.style.cursor = "pointer";
      closeReply.style.marginLeft = "auto";
      closeReply.addEventListener("click", this.removeReply);
      replyingDiv.append(closeReply);
      chatBoxFooter.prepend(replyingDiv);
    },

    removeReply() {
      event.target.closest(".replyDiv").remove();
      event.target.removeEventListener("click", this.removeReply);
      this.replyMessageID = null;
      this.replyingToMessage = null;
    },

    getUserRelatedUsersAnGroups(event, message) {
      const alreadyExistedModal = document.querySelector(".modal");
      if (alreadyExistedModal) alreadyExistedModal.remove();
      axios
        .get("/chat/user-related-users-and-groups", {
          params: {
            user_id: message.from,
          },
        })
        .then((response) => {
          this.createForwardMessageModal(
            response.data.users,
            response.data.groups,
            message
          );
        });
    },

    createForwardMessageModal(users, groups, message) {
      const modal = document.createElement("div");
      modal.classList.add("modal");
      modal.setAttribute("tabindex", "-1");
      modal.setAttribute("role", "dialog");
      modal.innerHTML = `
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Forward Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body forwardUsers" style="height:150px; overflow-y:scroll">
              </div>
              <div class="modal-body forwardGroups" style="max-height:150px; overflow-y:scroll">
                <p><i class="fas fa-users" style="font-size:30px"></i></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
      `;

      document.body.appendChild(modal);
      $(".modal").modal("show");
      const forwardUsersElement = document.querySelector(".forwardUsers");
      users.forEach((user) => {
        user.image = user.image == null ? "avatar.png" : user.image;
        const userDiv = document.createElement("div");
        const forwardBtn = document.createElement("button");
        forwardBtn.classList.add("btn", "btn-primary", "btn-sm");
        forwardBtn.innerText = "Forward";
        forwardBtn.style.float = "right";
        forwardBtn.dataset["userid"] = user.id;
        forwardBtn.dataset["message"] = JSON.stringify(message);
        forwardBtn.addEventListener("click", this.forward);
        userDiv.classList.add("forwardUserDiv");
        userDiv.style.marginBottom = "10px";
        const userImage = document.createElement("img");
        userImage.setAttribute("height", "30px");
        userImage.setAttribute("width", "30px");
        userImage.style.marginRight = "5px";
        userImage.src = `/images/${user.image}`;
        userDiv.append(userImage);
        userDiv.append(user.name);
        userDiv.append(forwardBtn);
        forwardUsersElement.append(userDiv);
      });

      groups.forEach((group) => {
        const forwardGroupsElement = document.querySelector(".forwardGroups");
        const forwardBtn = document.createElement("button");
        forwardBtn.classList.add("btn", "btn-primary", "btn-sm");
        forwardBtn.innerText = "Forward";
        forwardBtn.style.float = "right";
        forwardBtn.dataset["groupid"] = JSON.stringify(group.id);
        forwardBtn.dataset["message"] = JSON.stringify(message);
        forwardBtn.addEventListener("click", this.forward);
        const groupDiv = document.createElement("div");
        groupDiv.classList.add("forwardGroupDiv");
        groupDiv.style.marginBottom = "10px";
        groupDiv.append(group.name);
        groupDiv.append(forwardBtn);
        forwardGroupsElement.append(groupDiv);
      });
    },

    forward(event) {
      const userId = event.target.dataset["userid"];
      const message = JSON.parse(event.target.dataset["message"]);
      const groupid = event.target.dataset["groupid"];
      // console.log(message);
      // return false;
      if (message.message !== null) {
        this.sendMessage(null, message.message, userId, groupid, true);
      }
      if (message.type == "image") {
        this.sendImage(
          null,
          message.file,
          groupid ? null : userId,
          "image",
          userId ? null : groupid,
          true
        );
      }
      if (message.type == "file") {
        this.sendFile(
          null,
          message.file,
          groupid ? null : userId,
          "file",
          userId ? null : groupid,
          true
        );
      }
      if (message.type == "audio") {
        this.sendVoice(
          null,
          message.file,
          groupid ? null : userId,
          "audio",
          userId ? null : groupid,
          true
        );
      }
      document.querySelector(".modal").remove();
      document.querySelector(".modal-backdrop").remove();
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
  width: 360px;
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

.replyMessageImage {
  margin-left: 210px;
}

@media only screen and (max-width: 1000px) {
  .col-xs-4 {
    display: contents;
  }

  .top-bar {
    color: white;
    padding: 10px;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-evenly;
    height: 70px;
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
}
</style>