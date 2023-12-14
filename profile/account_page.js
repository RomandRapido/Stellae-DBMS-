const patterns = {
  name: /^[a-zA-Z_ ]+$/,
	username: /^[a-zA-Z_]+$/,
  email: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/,
  password: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()-_+=<>?]).{6,28}$/,
};
function validate_inputs(arr_text, arr) {
  arr_text.forEach(function (value) {
    let input_txt = document.getElementById(value);
    if (input_txt.value) {
      if (arr_text.index(value) == 2) {
        let userName = document.getElementById(value);
        if (userName.value) {
          if (!patterns.username.test(userName.value)) {
            alert(`Error: Invalid Username Detected`);
            return false;
          }
        }
      } else {
        if (!patterns.name.test(input_txt.value)) {
          alert(`Error: Number Detected on ${input_txt.name}`);
          return false;
        }
      }
    }
  });

  let email_txt = document.getElementById(arr[0]);
  if (email_txt.value) {
    if (!patterns.email.test(email_txt.value)) {
      alert(`Error: Invalid Email Detected`);
      return false;
    }
  }
  let password_txt = document.getElementById(arr[1]);
  if (password_txt.value) {
    if (!patterns.password.test(password_txt.value)) {
      alert(
        `Error: Password should be at least 6 characters with a capital letter, a small letter, a number, and one special character`
      );
      return false;
    }
  }
  console.log("ajax what?");
  $.ajax({
    url: "return_uniqueness.php",
    type: "POST",
    data: { action: "return_bool", data: [arr_text[2], email_txt.value] },
    success: function (response) {
      console.log(response);
      if (response.finding1 == true && response.finding2 == true) {
        return true;
      } else {
        if (response.finding1 == false && response.finding2 == false) {
          alert("Error: Email and Username is Taken");
          return false;
        } else if (response.finding1 == true && response.finding2 == false) {
          alert("Error: Username is Taken");
          return false;
        } else if (response.finding1 == false && response.finding2 == true) {
          alert("Error: Email is Taken");
          return false;
        } else {
          return false;
        }
      }
    },
  });
}

function intializeCheckBox() {
  const defButton = document.getElementById("Title");
  defButton.click();
  const defButton2 = document.getElementById("Recent");
  defButton2.click();
}

const likedStatus = [];

function initializeLikedStatus(index, initialLiked) {
  likedStatus[index] = initialLiked;
  if (initialLiked) {
    const likeButton = document.getElementById(`likeButton${index}`);
    likeButton.classList.toggle("liked", likedStatus[index]);
  }
}
globalUserId = -1;
function initializeUserId(localUserId) {
  globalUserId = localUserId;
}

function toggleLike(index, post_id) {
  if (typeof likedStatus[index] === "undefined") {
    likedStatus[index] = false;
  }

  const likeButton = document.getElementById(`likeButton${index}`);

  likedStatus[index] = !likedStatus[index];

  likeButton.classList.toggle("liked", likedStatus[index]);

  const likesCountElement = document.getElementById("likesCount" + index);
  const currentLikes = parseInt(likesCountElement.innerText, 10);

  if (likedStatus[index]) {
    likesCountElement.innerText = currentLikes + 1;
  } else {
    likesCountElement.innerText = currentLikes - 1;
  }

  const updateLikeEndpoint = "../feed/like_logic.php";
  const formData = new FormData();
  formData.append("post_id", post_id);

  fetch(updateLikeEndpoint, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.text();
    })
    .then((data) => {
      console.log("Like status updated on the server:", data);
    })
    .catch((error) => {
      console.error("Error updating like status:", error);
    });
}

let offset = 0;
function updateOffset() {
  var papersLength = document.getElementsByClassName("paper_for_each").length;
  offset = papersLength;
}

searchQuery = "";
function initializeSearchQuery(customQuery) {
  // debugger;
  searchQuery = customQuery;
}

let checkedFilterGlobal = ["Title"];
let searchVal = "";
let radioVal = "Recent";
function reChecked() {
  checkedFilterGlobal.forEach(function (item) {
    tempButton = document.getElementById(item);
    if (tempButton) {
      tempButton.click();
    }
  });
  tempSearchbar = document.getElementById("searchBar");
  tempSearchbar.value = searchVal;

  tempRadioBut = document.getElementById(radioVal);
  tempRadioBut.click();
}

function loadMorePapers() {
  const url = `../feed/generate_paper_feed.php?offset=${offset}&custom_query=${encodeURIComponent(
    searchQuery
  )}`;
  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      const paperContainer = document.querySelector(".paper_top_paper");
      paperContainer.insertAdjacentHTML("beforeend", html);
      // console.log(html);
      executeScriptsInElement(paperContainer);
      reChecked();
    })
    .catch((error) => {
      console.error("Error loading more papers:", error);
    });
}

function executeScriptsInElement(element) {
  const scripts = element.getElementsByTagName("script");
  for (let i = 0; i < scripts.length; i++) {
    const script = scripts[i];
    const scriptContent = script.textContent || script.innerText;
    const newScript = document.createElement("script");
    newScript.textContent = scriptContent;
    script.parentNode.replaceChild(newScript, script);
  }
  reChecked();
}

function hasScrolledPastPaper() {
  var paperDiv = document.getElementById("papers_location");

  var paperRect = paperDiv.getBoundingClientRect();
  var bottomOfPaper = paperRect.bottom;

  return bottomOfPaper <= window.innerHeight;
}

let scrollTimeout;

function handleScroll() {
  if (scrollTimeout) {
    clearTimeout(scrollTimeout);
  }

  scrollTimeout = setTimeout(function () {
    if (hasScrolledPastPaper()) {
      updateOffset();
      loadMorePapers();
    }
  }, 200);
}

window.addEventListener("scroll", handleScroll);

function getCheckedCheckbox(filters) {
  const checkedFilters = [];

  filters.forEach((filter) => {
    const checkbox = document.querySelector(`input[name='${filter}']`);
    if (checkbox.checked) {
      checkedFilters.push(filter);
    }
  });

  return checkedFilters;
}

function getCheckedFilter() {
  var form = document.getElementById("sorting");
  var checkedFilter = form.querySelector('input[name="sortBy"]:checked');
  // console.log(checkedFilter.id);
  if (checkedFilter) {
    radioVal = checkedFilter.id;
    return checkedFilter.id;
  } else {
    return null;
  }
}

function searchFor() {
  const formData = new FormData();
  const inputSearchBar = document.getElementById("searchBar");
  formData.append("searchKey", inputSearchBar.value);
  searchVal = inputSearchBar.value;
  const filters = ["Title", "Author", "Tags"];
  formData.append("type", globalTypeInscrip);
  const checkedFilters = getCheckedCheckbox(filters);
  checkedFilterGlobal = checkedFilters;
  checkedFilters.forEach((filter) => {
    formData.append(filter, true);
  });
  const checkedSorting = getCheckedFilter();
  formData.append("sort", checkedSorting);
  formData.append("userId", globalUserId);

  const endpoint = "getSearchQuery.php";

  fetch(endpoint, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.text();
    })
    .then((data) => {
      searchQuery = data;
      console.log(searchQuery);
      var papersContainer = document.getElementsByClassName("paper_for_each");

      var papersArray = Array.from(papersContainer);

      papersArray.forEach((paper) => {
        paper.remove();
      });
    })

    .catch((error) => {
      console.error("Error submitting data:", error);
    });
}

function goToEditPhp(postId) {
  const editEndpoint = `../postCreation/post_creation.php`;

  const newWindow = window.open(`${editEndpoint}?postId=${postId}`);

  if (
    !newWindow ||
    newWindow.closed ||
    typeof newWindow.closed === "undefined"
  ) {
    alert("Popup blocked. Please enable popups for this site.");
  }
}

function goToViewPhp(postId) {
  const viewEndpoint = `../view/view_account_file.php`;

  const newWindow = window.open(`${viewEndpoint}?PostId=${postId}`);

  if (
    !newWindow ||
    newWindow.closed ||
    typeof newWindow.closed === "undefined"
  ) {
    alert("Popup blocked. Please enable popups for this site.");
  }
}
globalTypeInscrip = "public";
function updateType(index) {
  let inscriptionButtons =
    document.getElementsByClassName("chosen_inscription");
  let currentInscript = document.getElementsByClassName(
    "selected_inscription"
  )[0];
  currentInscript.classList.toggle("selected_inscription");
  if (index == 0) {
    inscriptionButtons[index].classList.toggle("selected_inscription");
    globalTypeInscrip = "liked";
    searchFor();
  } else if (index == 1) {
    inscriptionButtons[index].classList.toggle("selected_inscription");
    globalTypeInscrip = "public";
    searchFor();
  } else {
    inscriptionButtons[index].classList.toggle("selected_inscription");
    globalTypeInscrip = "private";
    searchFor();
  }
}

function redirectToPage(index, UserId) {
  if (index == 0) {
    window.location.href = "../feed/home_page.php";
  } else if (index == 1) {
    window.location.href = `../Profile/account_view.php?UserId=${UserId}`;
  } else if (index == 2) {
    window.location.href = "../postCreation/post_creation.php";
  } else {
    var userConfirmed = confirm("Do you want to proceed with logging out?");
    if (userConfirmed) {
      window.location.href = "../logout.php";
    }
  }
}

function deleteThisPaper(postId) {
  var userConfirmation = confirm(
    "Are you sure you want to delete this post permanently?"
  );
  if (userConfirmation) {
    const formData = new FormData();
    formData.append("postId", postId);

    const endpoint = "../feed/deletePost.php";

    fetch(endpoint, {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text();
      })
      .then((data) => {
        location.reload();
      })
      .catch((error) => {
        console.error("Error submitting data:", error);
      });
  }
}

function autoScroll() {
  const targetElement = document.getElementById("paper_top_paper");
  targetElement.scrollIntoView({
    behavior: "smooth",
    block: "end",
  });
}

function followButtonLogic() {
  alert("Not yet implemented! Please comeback soon");
}

let followStatus = false;

function initializeFollowStatus(initialFollow) {
  followStatus = initialFollow;
  if (initialFollow) {
    try {
      const followButton = document.getElementsByClassName(`follow_btn`)[0];
      followButton.classList.toggle("followed");
      if (followStatus) {
        followButton.textContent = "Followed";
      } else {
        followButton.textContent = "Follow";
      }
    } catch {}
  }
}

function toggleFollow(userId) {
  const followButton = document.getElementsByClassName(`follow_btn`)[0];

  followStatus = !followStatus;
  followButton.classList.toggle("followed", followStatus);
  if (followStatus) {
    followButton.textContent = "Followed";
  } else {
    followButton.textContent = "Follow";
  }

  const updateLikeEndpoint = "followLogic.php";
  const formData = new FormData();
  formData.append("user_id", userId);

  fetch(updateLikeEndpoint, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.text();
    })
    .then((data) => {
      console.log("follow status updated on the server:", data);
    })
    .catch((error) => {
      console.error("Error updating follow   status:", error);
    });
}
