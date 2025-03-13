import styles from "../styles/searchCard.module.css";
import { Link } from "react-router-dom";

const SearchCard = ({ songs, onSongClick }) => {
  return (
    songs.length > 0 && (
      <div className={styles.searchCard}>
        <ul>
          {songs.map((song, index) => (
            <li key={index} onClick={() => onSongClick(song.id)}>
              <img src={song.album.images[0]?.url} alt={song.name} width="50" />
              <p>{song.name} by {song.artists[0]?.name}</p>
            </li>
          ))}
        </ul>
      </div>
    )
  );
};

export default SearchCard;
