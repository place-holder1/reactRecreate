import About from "../components/About";
import Wrapper from "../components/Wrapper";
import styles from "../styles/homepage.module.css";
import Navbar from "../components/Navbar";
import FileInput from "../components/FileInput";

const FileInputPage = () => {
    return (
        <Wrapper>
          <header className={styles["header"]}>
              <Navbar/>
            </header>
          <About />
          <FileInput/>
        </Wrapper>
      );
}

export default FileInputPage;