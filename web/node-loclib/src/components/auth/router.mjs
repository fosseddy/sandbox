import { Router } from "express";
import { isFormValid } from "#src/validator.mjs";
import * as crypto from "#src/crypto.mjs";
import { UserModel } from "./model.mjs";

const router = Router();

router.get("/login", (req, res) => {
    const form = { username: "", password: "", errors: {} };
    res.render("auth/login", { form });
});

router.post("/login", async (req, res) => {
    let { username, password } = req.body;
    let errors = {};

    if (!username || !username.trim()) {
        errors.username = "Username is required";
    } else if (username.length < 2 || username.length > 255) {
        errors.username = "Username must be between 2 and 255 characters long";
    }

    if (!password || !password.trim()) {
        errors.password = "Password is required";
    } else if (password.length < 6) {
        errors.password = "Password must be at least 6 characters";
    }

    if (!isFormValid({ errors })) {
        return res.render("auth/login", {
            form: { username, password, errors }
        });
    }

    const user = await UserModel.find({ username });
    if (!user) {
        errors.username = "Wrong credentials";
        return res.render("auth/login", {
            form: { username, password, errors }
        });
    }

    const hash = (await crypto.pbkdf2(password, user.salt)).toString("hex");
    if (user.password !== hash) {
        errors.username = "Wrong credentials";
        return res.render("auth/login", {
            form: { username, password, errors }
        });
    }

    await req.session.generate(user.id);

    res.redirect("/catalog");
});

router.get("/register", (req, res) => {
    const form = { username: "", password: "", errors: {} };
    res.render("auth/register", { form });
});

router.post("/register", async (req, res) => {
    let { username, password } = req.body;
    let errors = {};

    if (!username || !username.trim()) {
        errors.username = "Username is required";
    } else if (username.length < 2 || username.length > 255) {
        errors.username = "Username must be between 2 and 255 characters long";
    } else if (await UserModel.find({ username })) {
        errors.username = "Username is already taken";
    }

    if (!password || !password.trim()) {
        errors.password = "Password is required";
    } else if (password.length < 6) {
        errors.password = "Password must be at least 6 characters";
    }

    if (!isFormValid({ errors })) {
        return res.render("auth/register", {
            form: { username, password, errors }
        });
    }

    const salt = (await crypto.randomBytes(16)).toString("hex");
    password = (await crypto.pbkdf2(password, salt)).toString("hex");

    const user = { username, salt, password };
    await UserModel.insert(user);

    res.redirect("/login");
});

router.get("/logout", async (req, res) => {
    await req.session.destroy();
    res.redirect("/login");
});

export { router };
