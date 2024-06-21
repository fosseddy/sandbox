import express from "express";
import { router as catalogRouter } from "#components/catalog/router.mjs";
import { router as genreRouter } from "#components/genre/router.mjs";
import { router as authorRouter } from "#components/author/router.mjs";
import { router as authRouter } from "#components/auth/router.mjs";
import { session } from "#components/auth/session.mjs";
import * as database from "#src/database.mjs";

try {
    await database.init();
    console.log(`MySQL successfully connected on ${database.getSocketAddr()}`);
} catch (err) {
    console.error(err);
    process.exit(1);
}

const app = express();

app.use(express.static("public"));

app.use(session({ secret: "cosmos" }));

app.use(express.urlencoded({ extended: false }));

app.set("view engine", "pug");
app.locals.basedir = app.get("views");

app.use("/", authRouter);
app.use("/catalog", catalogRouter);
app.use("/catalog/genre", genreRouter);
app.use("/catalog/author", authorRouter);

export { app };
