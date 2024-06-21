const express = require("express");
const fs = require("fs/promises");
const cors = require("cors");

const app = express();
app.use(cors());

app.get("/load", async (req, res) => {
    let content = "";

    content += await fs.readFile("./load-progress.html", { encoding: "utf-8" });
    content += await fs.readFile("./package.json", { encoding: "utf-8" });
    content += await fs.readFile("./package-lock.json", { encoding: "utf-8" });

    content += content;
    content += content;
    content += content;
    content += content;

    res.status(200).send(content);
});

app.listen(5000, () => console.log("Server is listening on port", 5000));
