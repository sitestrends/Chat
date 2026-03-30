const express = require("express");
const http = require("http");
const { Server } = require("socket.io");

const app = express();
const server = http.createServer(app);

// IMPORTANT: allow your domain (or use "*" for testing)
const io = new Server(server, {
  cors: {
    origin: "*", // later replace with your domain
    methods: ["GET", "POST"]
  }
});

io.on("connection", (socket) => {
  console.log("User connected:", socket.id);

  socket.on("send_message", (data) => {
    io.emit("receive_message", data);
  });

  socket.on("disconnect", () => {
    console.log("User disconnected:", socket.id);
  });
});

// IMPORTANT for Railway
const PORT = process.env.PORT || 3000;

server.listen(PORT, () => {
  console.log("Server running on port", PORT);
});
/*const io = require('socket.io')(3000, {
    cors: { origin: "*" }
});

io.on('connection', socket => {

    socket.on('join', userId => {
        socket.join("user_" + userId);
    });

    socket.on('sendMessage', data => {
        // send to that user
        io.to("user_" + data.user_id).emit('newMessage', data);
    });

}); */
/*
const io = require('socket.io')(3000, {
    cors: { origin: "*" }
});

console.log("✅ Socket server running on port 3000");

io.on('connection', socket => {
    console.log("🔌 Someone connected");

    socket.on('join', userId => {
        console.log("User joined:", userId);
        socket.join("user_" + userId);
    });
});*/

