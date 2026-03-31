const express = require("express");
const http = require("http");
const { Server } = require("socket.io");

const app = express();
const server = http.createServer(app);

// ✅ REQUIRED for Railway (health check)
app.get("/", (req, res) => {
  res.send("OK");
});

// ✅ Socket.IO setup
const io = new Server(server, {
  cors: {
    origin: "*"
  }
});

io.on("connection", (socket) => {
  console.log("User connected:", socket.id);

  socket.on("join_project", (project_id) => {
    socket.join("project_" + project_id);
  });

  socket.on("send_message", (data) => {
    io.to("project_" + data.project_id).emit("receive_message", data);
  });
});

// ✅ CRITICAL: Railway port
const PORT = process.env.PORT || 8080;

server.listen(PORT, () => {
  console.log("Server running on port", PORT);
});

/*const express = require("express");
const http = require("http");
const { Server } = require("socket.io");

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
  cors: {
    origin: "https://sitesfortrends.com",
    methods: ["GET", "POST"]
  }
});

app.get("/", (req, res) => {
  res.send("Server is running");
});

io.on("connection", (socket) => {
  console.log("User connected:", socket.id);

  socket.on("join_project", (project_id) => {
    socket.join("project_" + project_id);
  });

  socket.on("send_message", (data) => {
    io.to("project_" + data.project_id).emit("receive_message", data);
  });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log("Server running on port", PORT);
});   */