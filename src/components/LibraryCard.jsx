import style from "../styles/librarycard.module.css";
import PropTypes from "prop-types";

const LibraryCard = ({ image_url, name, title }) => {
    return (
        <div className={style.playlistCard}>
            <div className={style.playlistCardImage}>
                <img src={image_url} alt={name} />
            </div>
            <div className={style.playlistCardContent}>
                <p className={style.playlistName}>{name}</p>
                <p className={style.playlistTitle}>{title}</p>
            </div>
        </div>
    );
};

LibraryCard.propTypes = {
    image_url: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
    title: PropTypes.string,
};

export default LibraryCard;