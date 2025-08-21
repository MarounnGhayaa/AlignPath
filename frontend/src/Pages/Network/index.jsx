import "./style.css";
import MentorCard from "../../Components/MentorCard";
import { User } from "lucide-react";

const Network = () => {
  return (
    <div className="network-body">
      <h1>Connected Mentors</h1>
      <div className="network-mentors">
        <MentorCard
          image={<User />}
          name={"Merwen Gh"}
          position={"Senior Software Engineer at Google"}
          skills={["react", "js", "oop"]}
        />
        <MentorCard
          image={<User />}
          name={"Merwen Gh"}
          position={"Senior Software Engineer at Google"}
          skills={["react", "js", "oop"]}
        />
        <MentorCard
          image={<User />}
          name={"Merwen Gh"}
          position={"Senior Software Engineer at Google"}
          skills={["react", "js", "oop"]}
        />
      </div>
    </div>
  );
};

export default Network;
