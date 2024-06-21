import * as database from "#src/database.mjs";

const UserModel = database.Model.create("user");
const SessionModel = database.Model.create("session");

export { UserModel, SessionModel };
