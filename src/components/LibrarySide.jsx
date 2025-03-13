import styles from "../styles/library.module.css";
import LibraryCard from "./LibraryCard";

const img = "https://i.imghippo.com/files/Yxc9348ANk.png";

const samplePlaylists = [
  {
      image_url: img,
      name: "Liked Songs",
      title: "Liked songs",
  },
  {
    image_url: img,
    name: "Downloaded Songs",
    title: "Downloaded songs",
  },
];


const Library = () => {
  return (
    <div className={styles.library}>
      <div className={styles.sidebar}>
        <div className={styles.header}>
          <h4>Your Library</h4>
          <div>
            {samplePlaylists.map((playlist, index) => (
                <LibraryCard
                    key={index}
                    image_url={playlist.image_url}
                    name={playlist.name}
                    title={playlist.title}
                />
            ))}
        </div>
        </div>
        <nav className={styles.nav}>
        </nav>
        </div>
    </div>
  );
};

export default Library;