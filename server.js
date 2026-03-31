/*const express = require("express");
const http = require("http");
const { Server } = require("socket.io");

const app = express();
const server = http.createServer(app);

// IMPORTANT: allow your domain (or use "*" for testing)
const io = new Server(server, {
  cors: {
    origin: [
      "https://sitesfortrends.com",
      "http://localhost:3000"
    ],
    methods: ["GET", "POST"],
    credentials: true
  }
});
  
io.on("connection", (socket) => {

  socket.on("join_project", (project_id) => {
    socket.join("project_" + project_id);
  });

});*/




const express = require("express");
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
});
/*
io.on("connection", (socket) => {

  console.log("User connected:", socket.id);

  socket.on("send_message", (data) => {

    const room = "project_" + data.project_id; // define room based on project

    // join the socket to that room (server-side)
    socket.join(room);

    // emit only to that room
    io.to(room).emit("receive_message", data);
  });

}); */

// IMPORTANT for Railway
/*const PORT = process.env.PORT || 3000;

server.listen(PORT, () => {
  console.log("Server running on port", PORT);
});

io.on("connection", (socket) => {

  socket.on("join_project", (project_id) => {
    socket.join("project_" + project_id);
  });

  // ✅ ONLY ONE handler
  socket.on("send_message", (data) => {
    const room = "project_" + data.project_id;

    io.to(room).emit("receive_message", data);
  });

}); */
// MySQL connection setup
/*const mysql = require("mysql2");

const db = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "site_intake_details"
}); */
// Update Socket.IO connection handler to save messages to DB
/*io.on("connection", (socket) => {
  socket.on("send_message", (data) => {

    const { sender_id, receiver_id, message, project_id } = data;

    // Save to DB
    db.query(
      "INSERT INTO site_messages (sender_id, receiver_id, project_id, message) VALUES (?, ?, ?, ?)",
      [sender_id, receiver_id, project_id, message],
      (err, result) => {
        if (err) {
          console.error(err);
          return;
        }

        // Emit to clients
        io.emit("receive_message", {
          id: result.insertId,
          sender_id,
          receiver_id,
          message,
          project_id
        });
      }
    );
  });
}); */

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

