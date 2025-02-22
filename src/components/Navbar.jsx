import styles from "../styles/navbar.module.css";
import { Link } from "react-router-dom";

const Navbar = () => {
    return (
      <nav className={`${styles["navbar"]}`}>
        <ul>
          <li>
            <Link to="/">Home</Link>
          </li>
          <li>
          <Link to="/about">About</Link>
          </li>
          <li>
          <Link to="/add-profile">Add Profile</Link>
          </li>
        </ul>
        {/* <button onClick={updateMode}>
          {mode === "light" ? "Light Mode" : "Dark Mode"}
        </button> */}
      </nav>
    );
  };

export default Navbar;