import cookie from "cookie";
import signature from "cookie-signature";
import { randomBytes } from "#src/crypto.mjs";
import { UserModel, SessionModel } from "./model.mjs";

const SID = "EXPRESSID";

const Session = {
    id: null,
    user: null,
    secret: null,
    res: null
};

Session.generate = async function(userId) {
    const sid = (await randomBytes(16)).toString("hex");
    const ssid = signature.sign(sid, this.secret);

    const maxAge = 1000 * 60 * 60 * 24;
    const expires = new Date(Date.now() + maxAge);

    this.res.cookie(SID, ssid, {
        maxAge,
        expires,
        httpOnly: true,
        sameSite: true,
    });

    await SessionModel.insert({
        id: sid,
        user_id: userId,
        expires
    });
}

Session.destroy = async function() {
    this.res.clearCookie(SID);
    await SessionModel.remove(this.id);
}

function session(opts) {
    setInterval(async () => {
        const sessions = await SessionModel.findAll();
        let count = 0;
        for (const sess of sessions) {
            if (new Date(sess.expires).getTime() <= Date.now()) {
                console.log("removing session:", sess.id);
                await SessionModel.remove(sess.id);
                count++;
            }
        }
        console.log("deleted", count, "sessions");
    }, 1000 * 60 * 60 * 5);

    return async function (req, res, next) {
        if (req.session) {
            console.warn("SESSION ALREADY EXIST");
            return next();
        }

        const session = Object.create(Session);
        session.secret = opts.secret;
        session.res = res;

        req.session = session;

        let cookies = req.get("Cookie");
        if (!cookies) return next();

        cookies = cookie.parse(cookies);
        if (!(SID in cookies)) {
            return next();
        }

        const sid = signature.unsign(cookies[SID], session.secret);
        const sess = await SessionModel.find({ id: sid });
        if (!sess) {
            console.warn(`No session with id ${sid} found`);
            return next();
        }

        if (new Date(sess.expires).getTime() <= Date.now()) {
            console.warn("Session has expired");
            await SessionModel.remove(sess.id);
            return next();
        }

        const user = await UserModel.find({ id: sess.user_id });
        if (!user) {
            console.warn(`No user with id ${sess.user_id} and ` +
                         `session id ${sid} found`);
            await SessionModel.remove(sess.id);
            return next();
        }

        delete user.password;
        delete user.salt;

        session.id = sid;
        session.user = user;

        req.session = session;
        res.locals.user = user

        next();
    }
}

export { session };
